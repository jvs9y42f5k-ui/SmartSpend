<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
        ]);

        $request->user()->goals()->create($validated);

        return redirect()->back()->with('success', 'Goal created successfully.');
    }

    public function deposit(Request $request, \App\Models\Goal $goal)
    {
        abort_unless($goal->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $goal->increment('saved_amount', $validated['amount']);

        return redirect()->back()->with('success', 'Deposit added.');
    }
}