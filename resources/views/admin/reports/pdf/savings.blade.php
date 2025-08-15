<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Savings Accounts Report</title>
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
        <h1>Savings Accounts Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Account Number</th>
                <th>Member</th>
                <th>Savings Type</th>
                <th>Current Balance</th>
                <th>Interest Rate</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($savingsAccounts as $account)
            <tr>
                <td>{{ $account->account_number }}</td>
                <td>{{ $account->member ? $account->member->first_name . ' ' . $account->member->last_name : '' }}</td>
                <td>{{ $account->savingsType ? $account->savingsType->name : '' }}</td>
                <td>{{ number_format($account->current_balance, 2) }}</td>
                <td>{{ $account->interest_rate }}%</td>
                <td>{{ $account->status }}</td>
                <td>{{ $account->created_at->format('Y-m-d') }}</td>
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