<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a transaction and, when linked to a goal, increases that goal\'s saved amount', function () {
    $user = User::factory()->create();

    $goal = $user->goals()->create([
        'name' => 'New Laptop',
        'target_amount' => 500,
        'saved_amount' => 0,
    ]);

    $response = $this->actingAs($user)->post('/transactions', [
        'title' => 'Paycheck',
        'date' => now()->format('Y-m-d'),
        'amount' => 100,
        'type' => 'income',
        'category' => 'Salary',
        'goal_id' => $goal->id,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'goal_id' => $goal->id,
        'title' => 'Paycheck',
    ]);

    expect((float) $goal->fresh()->saved_amount)->toBe(100.0);
});

it('does not let a guest (not logged in) create a transaction', function () {
    $response = $this->post('/transactions', [
        'title' => 'Should fail',
        'date' => now()->format('Y-m-d'),
        'amount' => 50,
        'type' => 'expense',
        'category' => 'Groceries',
    ]);

    $response->assertRedirect('/login');
});