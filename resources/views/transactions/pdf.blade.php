<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        .user-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .user-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .user-info p {
            margin: 5px 0;
            color: #666;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item h4 {
            margin: 0;
            color: #333;
            font-size: 14px;
        }
        .stat-item p {
            margin: 5px 0 0 0;
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .sent {
            color: #dc3545;
            font-weight: bold;
        }
        .received {
            color: #28a745;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction History Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="user-info">
        <h3>Account Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
        <p><strong>Current Balance:</strong> ${{ number_format($user->balance, 2) }}</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <h4>Total Transactions</h4>
            <p>{{ $transactions->count() }}</p>
        </div>
        <div class="stat-item">
            <h4>Total Sent</h4>
            <p>${{ number_format($transactions->where('sender_id', $user->id)->sum('amount'), 2) }}</p>
        </div>
        <div class="stat-item">
            <h4>Total Received</h4>
            <p>${{ number_format($transactions->where('receiver_id', $user->id)->sum('amount'), 2) }}</p>
        </div>
    </div>

    <h3>Transaction Details</h3>
    @if($transactions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Counterparty</th>
                    <th>Status</th>
                    <th>Transaction ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($transaction->sender_id === $user->id)
                                <span class="sent">Sent</span>
                            @else
                                <span class="received">Received</span>
                            @endif
                        </td>
                        <td>${{ number_format($transaction->amount, 2) }}</td>
                        <td>
                            @if($transaction->sender_id === $user->id)
                                To: {{ $transaction->receiver->name }} ({{ $transaction->receiver->phone }})
                            @else
                                From: {{ $transaction->sender->name }} ({{ $transaction->sender->phone }})
                            @endif
                        </td>
                        <td>{{ $transaction->status ?? 'Completed' }}</td>
                        <td>{{ $transaction->id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #666; font-style: italic;">No transactions found.</p>
    @endif

    <div class="footer">
        <p>This report was generated by Gganbu Banking System</p>
        <p>For any questions, please contact customer support</p>
    </div>
</body>
</html>
