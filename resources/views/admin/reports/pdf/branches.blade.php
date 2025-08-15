<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Branch Performance Report</title>
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
        <h1>Branch Performance Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Branch Name</th>
                <th>Location</th>
                <th>Manager</th>
                <th>Staff Count</th>
                <th>Member Count</th>
                <th>Active Loans</th>
                <th>Loan Portfolio</th>
                <th>Savings Accounts</th>
                <th>Savings Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->location }}</td>
                <td>{{ $branch->manager ? $branch->manager->name : 'Not Assigned' }}</td>
                <td>{{ $branch->staff_count ?? 0 }}</td>
                <td>{{ $branch->members->count() }}</td>
                <td>{{ $branch->loans->where('status', 'active')->count() }}</td>
                <td>{{ number_format($branch->loans->sum('amount'), 2) }}</td>
                <td>{{ $branch->savingsAccounts->count() }}</td>
                <td>{{ number_format($branch->savingsAccounts->sum('balance'), 2) }}</td>
                <td>{{ $branch->status }}</td>
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