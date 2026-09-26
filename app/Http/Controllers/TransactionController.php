<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of user transactions with filters, search, and sorting.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $query = Transaction::with('category')->where('user_id', $userId);

        // Search in notes or category name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Type (income / expense)
        if ($type = $request->input('type')) {
            if (in_array($type, ['income', 'expense'])) {
                $query->where('type', $type);
            }
        }

        // Filter by Category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by Payment Method / Sumber Dana
        if ($paymentMethod = $request->input('payment_method')) {
            if (isset(Transaction::USER_WALLET_GROUPS[$paymentMethod])) {
                $query->whereIn('payment_method', Transaction::USER_WALLET_GROUPS[$paymentMethod]['methods']);
            } elseif (array_key_exists($paymentMethod, Transaction::PAYMENT_METHODS)) {
                $query->where('payment_method', $paymentMethod);
            }
        }

        // Filter by Month and Year
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year', date('Y'));

        if ($selectedMonth && $selectedMonth !== 'all') {
            $query->whereMonth('date', $selectedMonth);
        }
        if ($selectedYear && $selectedYear !== 'all') {
            $query->whereYear('date', $selectedYear);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('order', 'desc');

        if (in_array($sortBy, ['date', 'amount', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        // Clone query for calculating filtered summary totals before pagination
        $statsQuery = clone $query;
        $totalIncome = (float) (clone $statsQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (float) (clone $statsQuery)->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $transactions = $query->paginate(15)->withQueryString();

        $categories = Category::orderBy('type')->orderBy('name')->get();
        $paymentMethods = Transaction::getAvailablePaymentMethods(Auth::user());

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'paymentMethods',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'payment_method' => 'nullable|string|in:' . implode(',', array_keys(Transaction::PAYMENT_METHODS)),
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'payment_method.in' => 'Metode atau asal dana tidak valid.',
        ]);

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'] ?? 'Cash',
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View|RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($transaction->isLockedFor(Auth::user())) {
            return redirect()->route('transactions.index')->with('error', 'Pemasukan bawaan akun demo dilindungi dan tidak dapat diedit.');
        }

        $categories = Category::all();
        $paymentMethods = Transaction::getAvailablePaymentMethods(Auth::user());
        return view('transactions.edit', compact('transaction', 'categories', 'paymentMethods'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($transaction->isLockedFor(Auth::user())) {
            return redirect()->route('transactions.index')->with('error', 'Pemasukan bawaan akun demo dilindungi dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'payment_method' => 'nullable|string|in:' . implode(',', array_keys(Transaction::PAYMENT_METHODS)),
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_method.in' => 'Metode atau asal dana tidak valid.',
        ]);

        $validated['payment_method'] = $validated['payment_method'] ?? 'Cash';

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($transaction->isLockedFor(Auth::user())) {
            return redirect()->back()->with('error', 'Pemasukan bawaan akun demo dilindungi dan tidak dapat dihapus.');
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Export transactions to PDF using barryvdh/laravel-dompdf.
     */
    public function exportPdf(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $query = Transaction::with('category')->where('user_id', $userId);

        // Apply same filters as table
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->input('type')) {
            if (in_array($type, ['income', 'expense'])) {
                $query->where('type', $type);
            }
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            if (isset(Transaction::USER_WALLET_GROUPS[$paymentMethod])) {
                $query->whereIn('payment_method', Transaction::USER_WALLET_GROUPS[$paymentMethod]['methods']);
            } elseif (array_key_exists($paymentMethod, Transaction::PAYMENT_METHODS)) {
                $query->where('payment_method', $paymentMethod);
            }
        }

        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year', date('Y'));

        if ($selectedMonth && $selectedMonth !== 'all') {
            $query->whereMonth('date', $selectedMonth);
        }
        if ($selectedYear && $selectedYear !== 'all') {
            $query->whereYear('date', $selectedYear);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $filterLabel = ($selectedMonth && $selectedMonth !== 'all') 
            ? Carbon::create()->month($selectedMonth)->locale('id')->isoFormat('MMMM') . " {$selectedYear}"
            : "Tahun {$selectedYear}";

        // Cek utang yang belum lunas
        $unpaidDebts = \App\Models\Debt::where('user_id', $userId)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get()
            ->filter(fn($d) => $d->remaining_amount > 0);

        $totalUnpaidDebt = (float) $unpaidDebts->sum(fn($d) => $d->remaining_amount);
        $hasUnpaidDebt = $totalUnpaidDebt > 0 && $unpaidDebts->count() > 0;

        $pdf = Pdf::loadView('transactions.pdf', compact(
            'transactions',
            'user',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'filterLabel',
            'unpaidDebts',
            'totalUnpaidDebt',
            'hasUnpaidDebt'
        ))->setPaper('a4', 'portrait');

        $fileName = 'Laporan_Keuangan_BudgetingMe_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
