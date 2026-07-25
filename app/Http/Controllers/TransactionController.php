<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'goal_id' => 'nullable|exists:goals,id',
        ]);

        $request->user()->transactions()->create($validated);

        if (!empty($validated['goal_id'])) {
            $goal = $request->user()->goals()->find($validated['goal_id']);
            if ($goal) {
                $goal->increment('saved_amount', $validated['amount']);
            }
        }

        return redirect()->back()->with('success', 'Transaction added successfully.');
    }
}