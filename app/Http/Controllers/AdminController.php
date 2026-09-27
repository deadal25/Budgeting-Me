<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the Admin panel overview with all users and stats.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        // Platform-wide aggregate stats
        $totalUsers = User::where('role', 'user')->count();
        $totalTransactions = Transaction::count();
        $totalPlatformIncome = (float) Transaction::where('type', 'income')->sum('amount');
        $totalPlatformExpense = (float) Transaction::where('type', 'expense')->sum('amount');
        $totalPlatformBalance = $totalPlatformIncome - $totalPlatformExpense;

        // Query users with eager aggregates
        $usersQuery = User::where('role', 'user')
            ->withCount('transactions');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $platformAccountBalances = Transaction::getPlatformAccountBalances();
        $registrationCodeDetails = \App\Models\AppSetting::getRegistrationCodeDetails();

        return view('admin.index', compact(
            'users',
            'totalUsers',
            'totalTransactions',
            'totalPlatformIncome',
            'totalPlatformExpense',
            'totalPlatformBalance',
            'platformAccountBalances',
            'registrationCodeDetails',
            'search'
        ));
    }

    /**
     * Regenerate the 4-character registration code (random) and reset 7-day period.
     */
    public function regenerateRegistrationCode(Request $request): \Illuminate\Http\RedirectResponse
    {
        $newCode = \App\Models\AppSetting::regenerateRegistrationCode();

        return redirect()->back()->with('success', "Kode pendaftaran baru berhasil diacak: {$newCode}. Kode ini aktif dan berlaku selama 7 hari ke depan.");
    }

    /**
     * Set a custom 4-character registration code.
     */
    public function updateRegistrationCode(Request $request): \Illuminate\Http\RedirectResponse
    {
        $rawCode = $request->input('code') ?? $request->input('custom_code');
        $request->merge(['code' => $rawCode]);

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:4', 'alpha_num'],
        ], [
            'code.required' => 'Kode pendaftaran wajib diisi.',
            'code.size' => 'Kode pendaftaran harus terdiri tepat 4 karakter (huruf atau angka).',
            'code.alpha_num' => 'Kode pendaftaran hanya boleh berisi kombinasi huruf dan angka.',
        ]);

        $savedCode = \App\Models\AppSetting::setCustomRegistrationCode($validated['code']);

        return redirect()->back()->with('success', "Kode pendaftaran berhasil diubah menjadi: {$savedCode}. Berlaku selama 7 hari.");
    }

    /**
     * Show detailed financial profile for a specific user (Read-Only).
     */
    public function showUser(User $user): View
    {
        $userTotalIncome = (float) $user->transactions()->where('type', 'income')->sum('amount');
        $userTotalExpense = (float) $user->transactions()->where('type', 'expense')->sum('amount');
        $userBalance = $userTotalIncome - $userTotalExpense;
        $userAccountBalances = $user->getAccountBalances();

        // User transactions
        $transactions = $user->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Expense by category for user
        $categoryExpenses = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->selectRaw('categories.name as category_name, categories.color as category_color, SUM(transactions.amount) as total_amount')
            ->groupBy('categories.name', 'categories.color')
            ->orderByDesc('total_amount')
            ->get();

        $chartLabels = $categoryExpenses->pluck('category_name')->toArray();
        $chartData = $categoryExpenses->pluck('total_amount')->toArray();
        $chartColors = $categoryExpenses->pluck('category_color')->map(function ($color) {
            return $color ?: '#10B981';
        })->toArray();

        // User budgets
        $now = Carbon::now();
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->get();

        // User debts
        $userDebts = $user->debts()->orderBy('created_at', 'desc')->get();
        $userTotalUnpaidDebt = $user->total_unpaid_debt;

        return view('admin.show_user', compact(
            'user',
            'userTotalIncome',
            'userTotalExpense',
            'userBalance',
            'userAccountBalances',
            'transactions',
            'chartLabels',
            'chartData',
            'chartColors',
            'budgets',
            'userDebts',
            'userTotalUnpaidDebt'
        ));
    }

    /**
     * Update a user's password directly from the Admin Panel.
     */
    public function updateUserPassword(Request $request, User $user): RedirectResponse
    {
        // Prevent modifying main admin if caller isn't that admin
        $mainAdmins = ['alqad.ri2505@gmail.com', 'alqadri2505@gmail.com'];
        if (in_array($user->email, $mainAdmins) && !in_array(auth()->user()->email, $mainAdmins)) {
            return redirect()->back()->with('error', 'Hanya administrator utama yang dapat mengubah kata sandi akun ini.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        $newPassword = $validated['password'];

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()
            ->with('success', "Kata sandi untuk pengguna {$user->name} ({$user->email}) berhasil diperbarui!")
            ->with('new_password_info', [
                'user_name' => $user->name,
                'email' => $user->email,
                'password' => $newPassword,
            ]);
    }

    /**
     * Send password reset link to user's registered email.
     */
    public function sendUserPasswordResetLink(Request $request, User $user): RedirectResponse
    {
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', "Link reset kata sandi telah berhasil dikirimkan ke email {$user->email}. Pengguna dapat langsung membuka email tersebut untuk mengganti kata sandinya.");
        }

        return redirect()->back()->with('error', 'Gagal mengirim link reset kata sandi: ' . __($status));
    }

    /**
     * Delete a user account and their associated transactions, budgets, and debts.
     */
    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isAdmin() || in_array($user->email, ['alqad.ri2505@gmail.com', 'alqadri2505@gmail.com'])) {
            return redirect()->back()->with('error', 'Akun Administrator tidak dapat dihapus.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        DB::transaction(function () use ($user) {
            $user->debts()->delete();
            $user->budgets()->delete();
            $user->transactions()->delete();
            $user->delete();
        });

        return redirect()->route('admin.index')
            ->with('success', "Akun pengguna {$userName} ({$userEmail}) beserta seluruh data keuangannya berhasil dihapus dari sistem.");
    }
}
