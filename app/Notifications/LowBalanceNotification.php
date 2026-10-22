<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(public Account $account) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'account_id' => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance' => $this->account->balance,
            'minimum_balance' => $this->account->minimum_balance,
            'message' => "Your account {$this->account->account_number} has a low balance of {$this->account->balance}. Minimum required: {$this->account->minimum_balance}.",
        ];
    }
}