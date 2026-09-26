<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    /**
     * Display the Budgetinku list and limit management.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $selectedMonth = (int) $request->input('month', $now->month);
        $selectedYear = (int) $request->input('year', $now->year);

        // Retrieve budgets for user in selected month and year
        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->orderByRaw('category_id IS NOT NULL')
            ->get();

        $overallBudget = $budgets->firstWhere('category_id', null);
        $categoryBudgets = $budgets->whereNotNull('category_id');

        // Only expense categories can have budget limits
        $expenseCategories = Category::where('type', 'expense')->orderBy('name')->get();

        return view('budgets.index', compact(
            'budgets',
            'overallBudget',
            'categoryBudgets',
            'expenseCategories',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Store or update a budget limit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2099',
            'limit_amount' => 'required|numeric|min:1000',
        ], [
            'limit_amount.min' => 'Batas anggaran minimal Rp 1.000.',
        ]);

        $userId = Auth::id();
        $categoryId = !empty($validated['category_id']) ? $validated['category_id'] : null;

        // Check if an existing budget is locked for demo account
        $existing = Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existing && $existing->isLockedFor($request->user())) {
            return redirect()->back()->with('error', 'Data budget bawaan akun demo dilindungi dan tidak dapat diubah.');
        }

        // Upsert budget
        Budget::updateOrCreate(
            [
                'user_id' => $userId,
                'category_id' => $categoryId,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'limit_amount' => $validated['limit_amount'],
            ]
        );

        return redirect()->route('budgets.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', 'Limit anggaran Budgetinku berhasil disimpan!');
    }

    /**
     * Update the specified budget limit.
     */
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($budget->isLockedFor($request->user())) {
            return redirect()->back()->with('error', 'Data budget bawaan akun demo dilindungi dan tidak dapat diedit.');
        }

        $validated = $request->validate([
            'limit_amount' => 'required|numeric|min:1000',
        ], [
            'limit_amount.min' => 'Batas anggaran minimal Rp 1.000.',
        ]);

        $budget->update([
            'limit_amount' => $validated['limit_amount'],
        ]);

        return redirect()->back()->with('success', 'Limit anggaran berhasil diperbarui!');
    }

    /**
     * Remove the specified budget limit.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($budget->isLockedFor(auth()->user())) {
            return redirect()->back()->with('error', 'Data budget bawaan akun demo dilindungi dan tidak dapat dihapus.');
        }

        $budget->delete();

        return redirect()->back()->with('success', 'Limit anggaran berhasil dihapus!');
    }
}
