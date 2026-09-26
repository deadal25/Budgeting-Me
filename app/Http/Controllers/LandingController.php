<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the landing page with the public transaction form.
     */
    public function index(): View
    {
        $categories = Category::all();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        $totalUsers = User::where('role', 'user')->count();
        $totalTransactions = Transaction::count();
        $paymentMethods = Transaction::USER_PAYMENT_METHODS;

        return view('welcome', compact('categories', 'incomeCategories', 'expenseCategories', 'totalUsers', 'totalTransactions', 'paymentMethods'));
    }

    /**
     * Real-time AJAX email check for the public transaction form.
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = trim(strtolower($request->query('email', '')));
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json([
                'registered' => true,
                'name' => $user->name,
                'message' => "✓ Email terdaftar atas nama {$user->name}. Transaksi akan tercatat di akun Anda.",
            ]);
        }

        return response()->json([
            'registered' => false,
            'name' => null,
            'message' => '✗ Email belum terdaftar. Periksa kembali penulisan email atau daftar akun terlebih dahulu.',
        ]);
    }

    /**
     * Process transaction submitted via public form.
     */
    public function storePublicTransaction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'payment_method' => 'nullable|string|in:' . implode(',', array_keys(Transaction::PAYMENT_METHODS)),
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'email.exists' => 'Email belum terdaftar. Silakan buat akun terlebih dahulu.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'payment_method.in' => 'Metode atau asal dana tidak valid.',
        ]);

        $user = User::where('email', strtolower($validated['email']))->firstOrFail();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'] ?? 'Cash',
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $typeLabel = $validated['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';
        $formattedAmount = 'Rp ' . number_format($validated['amount'], 0, ',', '.');

        return redirect()->back()->with('public_success', "Berhasil! {$typeLabel} sebesar {$formattedAmount} berhasil dicatat ke akun {$user->name}. Masuk ke akun Anda untuk melihat detailnya.");
    }

    /**
     * API to get categories by type.
     */
    public function getCategories(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $query = Category::query();

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        return response()->json($query->orderBy('name')->get());
    }
}
