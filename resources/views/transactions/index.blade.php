<!DOCTYPE html>
<html>
<head>
    <title>Transactions - Banking System</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="nav">
        <a href="{{ route('dashboard') }}">← Dashboard</a>
        <a href="{{ route('transactions.create') }}">+ New Transaction</a>
    </div>

    <h1>Transactions</h1>
    <p class="text-muted">All banking transactions (newest first)</p>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Notes</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $tx)
                <tr>
                    <td>{{ $tx->id }}</td>
                    <td><code>{{ $tx->reference_number ?? '—' }}</code></td>
                    <td>
                        <strong>{{ $tx->account->account_number }}</strong><br>
                        <small class="text-muted">{{ $tx->account->user->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <span class="badge badge-{{ $tx->type }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td>₱{{ number_format($tx->amount, 2) }}</td>
                    <td>{{ $tx->notes ?? '—' }}</td>
                    <td>{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem;">
                        No transactions yet. <a href="{{ route('transactions.create') }}" style="color: var(--pink-primary);">Create the first one</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</body>
</html>