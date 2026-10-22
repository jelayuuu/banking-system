<!DOCTYPE html>
<html>
<head>
    <title>New Transaction - Banking System</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="nav">
        <a href="{{ route('transactions.index') }}">← Back to Transactions</a>
    </div>

    <h1 style="text-align: center;">New Transaction</h1>

    <div class="form-container">
        @if ($errors->any())
            <div class="error-box">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf

            <label for="account_id">Account</label>
            <select name="account_id" id="account_id" required>
                <option value="">— Select an account —</option>
                @foreach ($accounts as $account)
                    <option
                        value="{{ $account->id }}"
                        data-balance="{{ $account->balance }}"
                        data-minimum="{{ $account->minimum_balance }}"
                        @selected(old('account_id') == $account->id)
                    >
                        {{ $account->account_number }} — {{ $account->user->name ?? 'N/A' }}
                        (Balance: ₱{{ number_format($account->balance, 2) }})
                    </option>
                @endforeach
            </select>
            @error('account_id')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <label for="type">Type</label>
            <select name="type" id="type" required>
                <option value="deposit" @selected(old('type') === 'deposit')>Deposit</option>
                <option value="withdrawal" @selected(old('type') === 'withdrawal')>Withdrawal</option>
            </select>
            @error('type')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <label for="amount">Amount (₱)</label>
            <input
                type="number"
                name="amount"
                id="amount"
                step="0.01"
                min="1"
                value="{{ old('amount') }}"
                placeholder="0.00"
                required
            >
            @error('amount')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <div id="balance-info" class="text-muted mt-4" style="display:none; padding: 1rem; background: #fff0f5; border-radius: 8px;"></div>

            <label for="notes">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3" placeholder="e.g., Salary deposit, bill payment...">{{ old('notes') }}</textarea>
            @error('notes')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <button type="submit">Submit Transaction</button>
        </form>
    </div>

    <script>
        const accountSelect = document.getElementById('account_id');
        const typeSelect    = document.getElementById('type');
        const amountInput   = document.getElementById('amount');
        const info          = document.getElementById('balance-info');

        function updatePreview() {
            const option = accountSelect.options[accountSelect.selectedIndex];
            const balance = parseFloat(option.dataset.balance || 0);
            const minimum = parseFloat(option.dataset.minimum || 0);
            const amount = parseFloat(amountInput.value || 0);
            const type = typeSelect.value;

            if (!accountSelect.value || !amount) {
                info.style.display = 'none';
                return;
            }

            const newBalance = type === 'deposit' ? balance + amount : balance - amount;
            const wouldDropBelow = type === 'withdrawal' && newBalance < minimum;

            info.style.display = 'block';
            info.style.background = wouldDropBelow ? '#ffe6e6' : '#fff0f5';
            info.style.color = wouldDropBelow ? '#991b1b' : '#d6336c';

            info.innerHTML =
                `Current balance: <strong>₱${balance.toFixed(2)}</strong><br>` +
                `New balance after ${type}: <strong>₱${newBalance.toFixed(2)}</strong>` +
                (wouldDropBelow
                    ? '<br><strong>⚠ This will be rejected — insufficient funds.</strong>'
                    : '');
        }

        accountSelect.addEventListener('change', updatePreview);
        typeSelect.addEventListener('change', updatePreview);
        amountInput.addEventListener('input', updatePreview);
    </script>
</body>
</html>