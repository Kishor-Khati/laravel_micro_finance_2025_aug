<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Share Bonus Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            margin: 0;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .info-item {
            display: inline-block;
            width: 32%;
            vertical-align: top;
        }
        .info-label {
            font-size: 10px;
            color: #666;
        }
        .info-value {
            font-weight: bold;
        }
        .summary-box {
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            width: 24%;
            text-align: center;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-right: 1%;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        .summary-note {
            font-size: 9px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .details-grid {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .details-box {
            width: 30%;
            margin-right: 3%;
            margin-bottom: 20px;
        }
        .details-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .details-label {
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Share Bonus Statement</h1>
        <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p>Branch: {{ request('branch_id') ? \App\Models\Branch::find(request('branch_id'))->name : 'All Branches' }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
    </div>
    
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Raw Income</div>
            <div class="summary-value">{{ number_format($data['total_raw_income'], 2) }}</div>
            <div class="summary-note">Total loan interest</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Share Bonus ({{ number_format((request('share_bonus_percentage') ?? 30), 2) }}%)</div>
            <div class="summary-value">{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</div>
            <div class="summary-note">Distributed to members</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Expenses</div>
            <div class="summary-value">{{ number_format($data['total_expenses'], 2) }}</div>
            <div class="summary-note">Total operational costs</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Net Income</div>
            <div class="summary-value">{{ number_format($data['final_balance'], 2) }}</div>
            <div class="summary-note">Raw income after expenses</div>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">Share Bonus Distribution</h2>
        <p>Share bonuses are distributed proportionally based on members' savings account balances.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Account Number</th>
                    <th>Member Name</th>
                    <th class="text-right">Savings Balance</th>
                    <th class="text-right">Proportion</th>
                    <th class="text-right">Bonus Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['share_bonus']['member_bonuses'] as $bonus)
                <tr>
                    <td>{{ $bonus['account_number'] }}</td>
                    <td>{{ $bonus['member_name'] }}</td>
                    <td class="text-right">{{ number_format($bonus['savings_balance'], 2) }}</td>
                    <td class="text-right">{{ number_format($bonus['proportion'] * 100, 2) }}%</td>
                    <td class="text-right">{{ number_format($bonus['bonus_amount'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3"></td>
                    <td class="text-right">100%</td>
                    <td class="text-right">{{ number_format($data['share_bonus']['total_distributed'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="page-break"></div>
    
    <div class="details-grid">
        <!-- Loans Summary -->
        <div class="details-box">
            <h3 class="details-title">Loans Summary</h3>
            <div class="details-row">
                <span class="details-label">Total Loans:</span>
                <span>{{ $data['loans']['total_loans'] }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Total Amount:</span>
                <span>{{ number_format($data['loans']['total_amount'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Total Interest:</span>
                <span>{{ number_format($data['loans']['total_interest'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Active Loans:</span>
                <span>{{ $data['loans']['active_loans'] }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Active Amount:</span>
                <span>{{ number_format($data['loans']['active_amount'], 2) }}</span>
            </div>
        </div>
        
        <!-- Savings Summary -->
        <div class="details-box">
            <h3 class="details-title">Savings Summary</h3>
            <div class="details-row">
                <span class="details-label">Total Accounts:</span>
                <span>{{ $data['savings']['total_accounts'] }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Total Balance:</span>
                <span>{{ number_format($data['savings']['total_balance'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Active Accounts:</span>
                <span>{{ $data['savings']['active_accounts'] }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Active Balance:</span>
                <span>{{ number_format($data['savings']['active_balance'], 2) }}</span>
            </div>
        </div>
        
        <!-- Transactions Summary -->
        <div class="details-box">
            <h3 class="details-title">Transactions Summary</h3>
            <div class="details-row">
                <span class="details-label">Deposits:</span>
                <span>{{ number_format($data['transactions']['deposits'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Withdrawals:</span>
                <span>{{ number_format($data['transactions']['withdrawals'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Loan Disbursements:</span>
                <span>{{ number_format($data['transactions']['loan_disbursements'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Loan Payments:</span>
                <span>{{ number_format($data['transactions']['loan_payments'], 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Total Transactions:</span>
                <span>{{ $data['transactions']['total_transactions'] }}</span>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p style="text-align: center; font-size: 10px; color: #666;">
            This is an automatically generated report. For any questions, please contact the administrator.
        </p>
    </div>
</body>
</html>