<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Executive Summary Report</title>
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
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
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
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .kpi-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        .kpi-title {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .kpi-change {
            font-size: 10px;
            margin-top: 5px;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
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
        <h1>Executive Summary Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        <p>Reporting Period: {{ date('Y-m-d', strtotime('-30 days')) }} to {{ date('Y-m-d') }}</p>
    </div>
    
    <div class="section">
        <div class="section-title">Key Performance Indicators</div>
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-title">Total Members</div>
                <div class="kpi-value">{{ number_format($data['members']['count']) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Active Members</div>
                <div class="kpi-value">{{ number_format($data['members']['active_count']) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Total Loans</div>
                <div class="kpi-value">{{ number_format($data['loans']['count']) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Active Loans</div>
                <div class="kpi-value">{{ number_format($data['loans']['active_count']) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Total Savings</div>
                <div class="kpi-value">{{ number_format($data['savings']['count']) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Savings Balance</div>
                <div class="kpi-value">${{ number_format($data['savings']['amount'], 2) }}</div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Financial Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Loan Portfolio</td>
                    <td>${{ number_format($data['loans']['amount'], 2) }}</td>
                </tr>
                <tr>
                    <td>Savings Balance</td>
                    <td>${{ number_format($data['savings']['amount'], 2) }}</td>
                </tr>
                <tr>
                    <td>Total Transactions</td>
                    <td>${{ number_format($data['transactions']['amount'], 2) }}</td>
                </tr>
                <tr>
                    <td>Total Expenses</td>
                    <td>${{ number_format($data['expenses']['amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Loan Portfolio Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Loan Status</th>
                    <th>Count</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Active Loans</td>
                    <td>{{ $data['loans']['active_count'] }}</td>
                    <td>${{ number_format($data['loans']['active_amount'], 2) }}</td>
                </tr>
                <tr>
                    <td>Pending Loans</td>
                    <td>{{ $data['loans']['pending_count'] }}</td>
                    <td>${{ number_format($data['loans']['pending_amount'], 2) }}</td>
                </tr>
                <tr>
                    <td>Completed Loans</td>
                    <td>{{ $data['loans']['completed_count'] }}</td>
                    <td>${{ number_format($data['loans']['completed_amount'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td>{{ $data['loans']['count'] }}</td>
                    <td>${{ number_format($data['loans']['amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        <p>MicroLendHub - Confidential Executive Summary</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>