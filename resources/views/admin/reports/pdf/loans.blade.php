<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Loans Report</title>
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
        <h1>Loans Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Loan Number</th>
                <th>Member</th>
                <th>Loan Type</th>
                <th>Principal</th>
                <th>Interest Rate</th>
                <th>Term</th>
                <th>Total Payable</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
            <tr>
                <td>{{ $loan->loan_number }}</td>
                <td>{{ $loan->member ? $loan->member->first_name . ' ' . $loan->member->last_name : '' }}</td>
                <td>{{ $loan->loanType ? $loan->loanType->name : '' }}</td>
                <td>{{ number_format($loan->principal_amount, 2) }}</td>
                <td>{{ $loan->interest_rate }}%</td>
                <td>{{ $loan->term_months }} months</td>
                <td>{{ number_format($loan->total_payable, 2) }}</td>
                <td>{{ number_format($loan->total_paid, 2) }}</td>
                <td>{{ number_format($loan->remaining_balance, 2) }}</td>
                <td>{{ $loan->status }}</td>
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