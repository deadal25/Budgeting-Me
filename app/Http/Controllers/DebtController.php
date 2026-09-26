<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DebtController extends Controller
{
    /**
     * Display a listing of debts.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $statusFilter = $request->query('status', 'all');

        $query = Debt::where('user_id', $user->id);

        if ($statusFilter === 'unpaid') {
            $query->where('status', 'unpaid');
        } elseif ($statusFilter === 'partial') {
            $query->where('status', 'partial');
        } elseif ($statusFilter === 'paid') {
            $query->where('status', 'paid');
        } elseif ($statusFilter === 'active') {
            $query->whereIn('status', ['unpaid', 'partial']);
        }

        $debts = $query->orderByRaw("CASE WHEN status = 'paid' THEN 2 ELSE 1 END")
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $allDebts = Debt::where('user_id', $user->id)->get();
        $totalUnpaidDebt = (float) $allDebts->where('status', '!=', 'paid')->sum(fn($d) => $d->remaining_amount);
        $totalPaidDebt = (float) $allDebts->sum('paid_amount');
        $totalAllDebt = (float) $allDebts->sum('amount');
        $unpaidCount = $allDebts->where('status', '!=', 'paid')->count();
        $paidCount = $allDebts->where('status', 'paid')->count();

        $paymentMethods = Transaction::getAvailablePaymentMethods($user);

        return view('debts.index', compact(
            'debts',
            'statusFilter',
            'totalUnpaidDebt',
            'totalPaidDebt',
            'totalAllDebt',
            'unpaidCount',
            'paidCount',
            'paymentMethods'
        ));
    }

    /**
     * Store a newly created debt.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'creditor' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'pay_on_salary' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $amount = (float) $validated['amount'];
        $paidAmount = isset($validated['paid_amount']) ? (float) $validated['paid_amount'] : 0.0;

        if ($paidAmount >= $amount) {
            $status = 'paid';
            $paidAmount = $amount;
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        Debt::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'creditor' => $validated['creditor'] ?? null,
            'amount' => $amount,
            'paid_amount' => $paidAmount,
            'due_date' => $validated['due_date'] ?? null,
            'pay_on_salary' => $request->has('pay_on_salary') ? true : false,
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Catatan utang berhasil ditambahkan!');
    }

    /**
     * Update the specified debt.
     */
    public function update(Request $request, Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'creditor' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'pay_on_salary' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $amount = (float) $validated['amount'];
        $paidAmount = isset($validated['paid_amount']) ? (float) $validated['paid_amount'] : (float) $debt->paid_amount;

        if ($paidAmount >= $amount) {
            $status = 'paid';
            $paidAmount = $amount;
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $debt->update([
            'title' => $validated['title'],
            'creditor' => $validated['creditor'] ?? null,
            'amount' => $amount,
            'paid_amount' => $paidAmount,
            'due_date' => $validated['due_date'] ?? null,
            'pay_on_salary' => $request->has('pay_on_salary') ? true : false,
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Catatan utang berhasil diperbarui!');
    }

    /**
     * Record a payment / installment for a debt.
     */
    public function pay(Request $request, Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validPaymentMethods = array_keys(Transaction::PAYMENT_METHODS);

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:' . implode(',', $validPaymentMethods),
            'payment_date' => 'nullable|date',
            'create_transaction' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $paymentAmount = (float) $validated['payment_amount'];
        $paymentMethod = $validated['payment_method'];
        $paymentDate = !empty($validated['payment_date']) ? $validated['payment_date'] : Carbon::now()->toDateString();
        $createTx = $request->boolean('create_transaction', true);

        // Record repayment on debt
        $debt->recordPayment($paymentAmount);

        // Optionally create transaction so wallet balances stay in sync
        if ($createTx) {
            $category = Category::where('name', 'Utang')
                ->where('type', 'expense')
                ->first();

            if (!$category) {
                $category = Category::firstOrCreate(
                    ['name' => 'Utang', 'type' => 'expense'],
                    ['icon' => 'shield-exclamation', 'color' => '#DC2626']
                );
            }

            $creditorNote = $debt->creditor ? " ke {$debt->creditor}" : '';
            $extraNote = !empty($validated['notes']) ? " ({$validated['notes']})" : '';

            Transaction::create([
                'user_id' => Auth::id(),
                'category_id' => $category->id,
                'type' => 'expense',
                'amount' => $paymentAmount,
                'date' => $paymentDate,
                'payment_method' => $paymentMethod,
                'description' => "Bayar Utang: {$debt->title}{$creditorNote}{$extraNote}",
            ]);
        }

        $formattedPaid = 'Rp ' . number_format($paymentAmount, 0, ',', '.');
        $msg = "Pembayaran utang sebesar {$formattedPaid} berhasil dicatat!";
        if ($debt->is_paid) {
            $msg .= " 🎉 Selamat, utang ini telah LUNAS!";
        } else {
            $msg .= " Sisa utang saat ini: {$debt->formatted_remaining_amount}.";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete a debt record.
     */
    public function destroy(Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $debt->delete();

        return redirect()->back()->with('success', 'Catatan utang berhasil dihapus.');
    }
}
