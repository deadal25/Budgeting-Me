<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Current month transactions summary
        $monthIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $monthExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $monthBalance = $monthIncome - $monthExpense;

        // All-time balance
        $allTimeBalance = $user->balance;

        // Category breakdown for current month expenses (for Pie/Doughnut Chart)
        $categoryExpenses = Transaction::query()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereYear('transactions.date', $currentYear)
            ->whereMonth('transactions.date', $currentMonth)
            ->selectRaw('categories.name as category_name, categories.color as category_color, SUM(transactions.amount) as total_amount')
            ->groupBy('categories.name', 'categories.color')
            ->orderByDesc('total_amount')
            ->get();

        $chartLabels = $categoryExpenses->pluck('category_name')->toArray();
        $chartData = $categoryExpenses->pluck('total_amount')->toArray();
        $chartColors = $categoryExpenses->pluck('category_color')->map(function ($color) {
            return $color ?: '#10B981';
        })->toArray();

        // Recent 10 transactions
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Budgetinku evaluation for this month
        $overallBudget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $categoryBudgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();

        // Determine main budget alert for popup/banner
        $budgetNotification = null;
        if ($overallBudget) {
            $budgetNotification = [
                'type' => 'overall',
                'title' => 'Batas Pengeluaran Bulanan',
                'limit' => $overallBudget->limit_amount,
                'spent' => $overallBudget->spent_amount,
                'remaining' => $overallBudget->remaining_amount,
                'percentage' => $overallBudget->percentage,
                'status' => $overallBudget->status, // safe, warning, danger
                'message' => $overallBudget->status === 'danger'
                    ? "Limit Terlampaui! Pengeluaran Anda telah melebihi batas anggaran bulanan."
                    : ($overallBudget->status === 'warning'
                        ? "Hati-hati jangan boros! Anda telah menggunakan {$overallBudget->percentage}% dari anggaran bulan ini."
                        : "Sisa anggaran bulan ini: Rp " . number_format($overallBudget->remaining_amount, 0, ',', '.')),
            ];
        }

        // Available categories and payment methods for modal quick add
        $categories = Category::all();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');
        $paymentMethods = Transaction::getAvailablePaymentMethods($user);
        $accountBalances = $user->getDisplayAccountBalances();
        $isAdmin = $user->isAdmin();

        // Debt Tracking data
        $unpaidDebts = $user->debts()
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();
        $totalUnpaidDebt = (float) $unpaidDebts->sum(fn($d) => $d->remaining_amount);
        $unpaidDebtsCount = $unpaidDebts->count();
        $hasUnpaidDebt = ($unpaidDebtsCount > 0);
        $salaryDebtsCount = $unpaidDebts->where('pay_on_salary', true)->count();

        return view('dashboard', compact(
            'monthIncome',
            'monthExpense',
            'monthBalance',
            'allTimeBalance',
            'chartLabels',
            'chartData',
            'chartColors',
            'recentTransactions',
            'overallBudget',
            'categoryBudgets',
            'budgetNotification',
            'categories',
            'incomeCategories',
            'expenseCategories',
            'paymentMethods',
            'accountBalances',
            'unpaidDebts',
            'totalUnpaidDebt',
            'unpaidDebtsCount',
            'hasUnpaidDebt',
            'salaryDebtsCount',
            'isAdmin'
        ));
    }
}
