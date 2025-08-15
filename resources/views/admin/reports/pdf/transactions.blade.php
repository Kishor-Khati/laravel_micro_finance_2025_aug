<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transactions Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transactions Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Account</th>
                <th>Member</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_id }}</td>
                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                <td>
                    @if($transaction->savings_account_id)
                        {{ $transaction->savingsAccount ? $transaction->savingsAccount->account_number : '' }} (Savings)
                    @elseif($transaction->loan_id)
                        {{ $transaction->loan ? $transaction->loan->loan_number : '' }} (Loan)
                    @endif
                </td>
                <td>
                    @if($transaction->savings_account_id && $transaction->savingsAccount && $transaction->savingsAccount->member)
                        {{ $transaction->savingsAccount->member->first_name }} {{ $transaction->savingsAccount->member->last_name }}
                    @elseif($transaction->loan_id && $transaction->loan && $transaction->loan->member)
                        {{ $transaction->loan->member->first_name }} {{ $transaction->loan->member->last_name }}
                    @endif
                </td>
                <td>{{ $transaction->transaction_type }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>MicroLendHub - Confidential Report</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>