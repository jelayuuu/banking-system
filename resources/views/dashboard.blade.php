<!DOCTYPE html>
<html>
<head>
    <title>Banking Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('transactions.index') }}">Transactions</a>
        <a href="{{ route('transactions.create') }}">New Transaction</a>
    </div>

    <h1>Banking Dashboard</h1>
    <p class="text-muted">Real-time bank statistics</p>

    <div class="grid">
        <div class="card">
            <h3>Total Accounts</h3>
            <div class="stat" id="total-accounts">-</div>
        </div>
        <div class="card">
            <h3>Total Money in Bank</h3>
            <div class="stat" id="total-money">-</div>
        </div>
        <div class="card">
            <h3>Transactions Today</h3>
            <div class="stat" id="total-transactions">-</div>
        </div>
    </div>

    <p id="last-updated" class="text-muted mt-4"></p>

    <script>
        async function fetchStats() {
            try {
                const res = await fetch('{{ url('/api/dashboard/stats') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                document.getElementById('total-accounts').textContent = data.total_accounts;
                document.getElementById('total-money').textContent =
                    '₱' + parseFloat(data.total_money).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('total-transactions').textContent = data.total_transactions_today;
                document.getElementById('last-updated').textContent =
                    'Last updated: ' + new Date().toLocaleTimeString();
            } catch (e) {
                console.error('Failed to fetch stats', e);
            }
        }

        fetchStats();
        setInterval(fetchStats, 30000);
    </script>
</body>
</html>