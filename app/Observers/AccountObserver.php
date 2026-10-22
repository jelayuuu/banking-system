<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountObserver
{
    public function created(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = Account::lockForUpdate()->findOrFail($transaction->account_id);

            $newBalance = $account->balance;

            if ($transaction->type === 'deposit') {
                $newBalance += $transaction->amount;
            } else { // withdrawal or transfer
                $newBalance -= $transaction->amount;

                if ($newBalance < $account->minimum_balance) {
                    throw ValidationException::withMessages([
                        'amount' => "Insufficient funds. Balance would fall below the minimum balance of {$account->minimum_balance}.",
                    ]);
                }
            }

            $account->balance = $newBalance;
            $account->save();

            if (in_array($transaction->type, ['withdrawal', 'transfer'])
                && $account->balance <= $account->minimum_balance) {
                $account->user->notify(new LowBalanceNotification($account));
            }
        });
    }
}