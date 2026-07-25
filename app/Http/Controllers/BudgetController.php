<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'limit_amount' => 'required|numeric|min:0',
        ]);

        $request->user()->budgets()->updateOrCreate(
            ['category' => $validated['category']],
            ['limit_amount' => $validated['limit_amount']]
        );

        return redirect()->back()->with('success', 'Budget limit updated.');
    }
}