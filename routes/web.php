<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $transactions = auth()->user()->transactions()->orderByDesc('date')->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyTransactions = $transactions->filter(function ($t) use ($currentMonth, $currentYear) {
            $date = \Carbon\Carbon::parse($t->date);
            return $date->month === $currentMonth && $date->year === $currentYear;
        });

        $totalIncome = $monthlyTransactions->where('type', 'income')->sum('amount');
        $totalExpenses = $monthlyTransactions->where('type', 'expense')->sum('amount');
        $remaining = $totalIncome - $totalExpenses;

        return view('home', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'remaining' => $remaining,
            'recentTransactions' => $transactions->take(4),
        ]);
    })->name('home');

    Route::get('/analytics', function () {
        $transactions = auth()->user()->transactions()->get();

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $monthLabels = $months->map(fn ($m) => $m->format('M'));

        $incomeData = $months->map(function ($m) use ($transactions) {
            return $transactions->where('type', 'income')
                ->filter(fn ($t) => \Carbon\Carbon::parse($t->date)->format('Y-m') === $m->format('Y-m'))
                ->sum('amount');
        })->values();

        $expenseData = $months->map(function ($m) use ($transactions) {
            return $transactions->where('type', 'expense')
                ->filter(fn ($t) => \Carbon\Carbon::parse($t->date)->format('Y-m') === $m->format('Y-m'))
                ->sum('amount');
        })->values();

        $palette = ['#8b5cf6', '#06b6d4', '#f59e0b', '#22C55E', '#f43f5e', '#3b82f6', '#eab308', '#ec4899'];

        $categories = $transactions->where('type', 'expense')->pluck('category')->unique()->values();

        $categoryDatasets = $categories->map(function ($category, $index) use ($months, $transactions, $palette) {
            return [
                'label' => $category,
                'data' => $months->map(function ($m) use ($category, $transactions) {
                    return $transactions->where('type', 'expense')
                        ->where('category', $category)
                        ->filter(fn ($t) => \Carbon\Carbon::parse($t->date)->format('Y-m') === $m->format('Y-m'))
                        ->sum('amount');
                })->values(),
                'backgroundColor' => $palette[$index % count($palette)],
            ];
        })->values();

        $cumulativeByCategory = $transactions->where('type', 'expense')->groupBy('category')->map->sum('amount');

        return view('analytics', [
            'monthLabels' => $monthLabels,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'categoryDatasets' => $categoryDatasets,
            'cumulativeLabels' => $cumulativeByCategory->keys()->values(),
            'cumulativeData' => $cumulativeByCategory->values(),
            'cumulativeColors' => array_slice($palette, 0, $cumulativeByCategory->count()),
        ]);
    });

    Route::get('/budget', function () {
        $categories = ['House', 'Credit Card', 'Transportation', 'Groceries', 'Shopping', 'General Savings'];

        $limits = auth()->user()->budgets()->pluck('limit_amount', 'category');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyExpenses = auth()->user()->transactions()
            ->where('type', 'expense')
            ->get()
            ->filter(function ($t) use ($currentMonth, $currentYear) {
                $date = \Carbon\Carbon::parse($t->date);
                return $date->month === $currentMonth && $date->year === $currentYear;
            });

        $budgetData = collect($categories)->map(function ($category) use ($limits, $monthlyExpenses) {
            $spent = $monthlyExpenses->where('category', $category)->sum('amount');
            $limit = $limits->get($category, 0);
            return [
                'category' => $category,
                'spent' => $spent,
                'limit' => $limit,
                'percent' => $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0,
                'overBudget' => $limit > 0 && $spent > $limit,
            ];
        });

        return view('budget', [
            'budgetData' => $budgetData,
            'totalLimit' => $budgetData->sum('limit'),
            'totalSpent' => $budgetData->sum('spent'),
            'overBudgetCount' => $budgetData->where('overBudget', true)->count(),
            'onTrackCount' => $budgetData->where('overBudget', false)->count(),
        ]);
    });

    Route::get('/goals', function () {
        $goals = auth()->user()->goals()->latest()->get();
        return view('goals', ['goals' => $goals]);
    });

    Route::patch('/profile/salary', [ProfileController::class, 'updateSalary'])->name('profile.salary.update');

    Route::get('/tables', function () {
        $transactions = auth()->user()->transactions()->orderByDesc('date')->get();
        return view('tables', ['transactions' => $transactions]);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::post('/goals/{goal}', [GoalController::class, 'deposit'])->name('goals.deposit');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';