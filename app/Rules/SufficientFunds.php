<?php

namespace App\Rules;

use App\Models\Account;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SufficientFunds implements ValidationRule
{
    public function __construct(
        protected int $accountId,
        protected string $type
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type !== 'withdrawal' && $this->type !== 'transfer') {
            return;
        }

        $account = Account::find($this->accountId);

        if (!$account) {
            return;
        }

        if (($account->balance - (float) $value) < $account->minimum_balance) {
            $fail("Insufficient funds. Balance cannot fall below the minimum balance of {$account->minimum_balance}.");
        }
    }
}