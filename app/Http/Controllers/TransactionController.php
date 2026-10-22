<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Rules\SufficientFunds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account.user')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::with('user')->get();
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Custom rule: prevent overdraft before saving
        $request->validate([
            'amount' => [new SufficientFunds(
                (int) $validated['account_id'],
                $validated['type']
            )],
        ]);

        DB::transaction(function () use ($validated) {
            Transaction::create([
                'account_id' => $validated['account_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'reference_number' => 'REF-' . strtoupper(Str::random(10)),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction processed successfully.');
    }
}