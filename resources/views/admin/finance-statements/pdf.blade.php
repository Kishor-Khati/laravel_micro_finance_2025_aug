<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-box .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-box .label {
            font-weight: bold;
            color: #666;
        }
        .summary-box {
            margin-bottom: 30px;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            flex: 1;
            min-width: 120px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-item.income {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
        }
        .summary-item.bonus {
            background-color: #dbeafe;
            border: 1px solid #bfdbfe;
        }
        .summary-item.expense {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
        }
        .summary-item.net {
            background-color: #f3e8ff;
            border: 1px solid #e9d5ff;
        }
        .summary-item .amount {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .summary-item.income .amount {
            color: #059669;
        }
        .summary-item.bonus .amount {
            color: #2563eb;
        }
        .summary-item.expense .amount {
            color: #dc2626;
        }
        .summary-item.net .amount {
            color: #7c3aed;
        }
        .summary-item .label {
            font-size: 10px;
            color: #666;
        }
        .breakdown {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        .breakdown-section {
            flex: 1;
            min-width: 200px;
        }
        .breakdown-section h3 {
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .breakdown-label {
            color: #666;
        }
        .breakdown-value {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            margin-bottom: 15px;
            color: #1f2937;
        }
        .page-break {
            page-break-after: always;
        }
        .no-data {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 10px;
            border-radius: 5px;
            color: #92400e;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Finance Statement</h1>
        <p>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p>{{ request('branch_id') ? \App\Models\Branch::find(request('branch_id'))->name : 'All Branches' }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
    </div>
    
    <div class="section">
        <h2 class="section-title">Financial Summary</h2>
        
        <div class="summary-grid">
            <!-- Raw Income -->
            <div class="summary-item income">
                <div class="label">Raw Income</div>
                <div class="amount">{{ number_format($data['total_raw_income'], 2) }}</div>
                <div class="description">Loan interest earned</div>
            </div>
            
            <!-- Share Bonuses -->
            <div class="summary-item bonus">
                <div class="label">Share Bonuses</div>
                <div class="amount">{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</div>
                <div class="description">{{ $data['share_bonus']['members_with_savings']->count() }} members</div>
            </div>
            
            <!-- Expenses -->
            <div class="summary-item expense">
                <div class="label">Expenses</div>
                <div class="amount">{{ number_format($data['total_expenses'], 2) }}</div>
                <div class="description">Operational expenses</div>
            </div>
            
            <!-- Net Income -->
            <div class="summary-item net">
                <div class="label">Net Income</div>
                <div class="amount">{{ number_format($data['final_balance'], 2) }}</div>
                <div class="description">Raw income after expenses</div>
            </div>
        </div>
        
        <div class="breakdown">
            <!-- Loans Summary -->
            <div class="breakdown-section">
                <h3>Loans</h3>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Loans:</span>
                    <span class="breakdown-value">{{ $data['loans']['total'] }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Active Loans:</span>
                    <span class="breakdown-value">{{ $data['loans']['active'] }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Amount:</span>
                    <span class="breakdown-value">{{ number_format($data['loans']['amount'], 2) }}</span>
                </div>
            </div>
            
            <!-- Savings Summary -->
            <div class="breakdown-section">
                <h3>Savings</h3>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Accounts:</span>
                    <span class="breakdown-value">{{ $data['savings']['total'] }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Active Accounts:</span>
                    <span class="breakdown-value">{{ $data['savings']['active'] }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Balance:</span>
                    <span class="breakdown-value">{{ number_format($data['savings']['balance'], 2) }}</span>
                </div>
            </div>
            
            <!-- Transactions Summary -->
            <div class="breakdown-section">
                <h3>Transactions</h3>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Transactions:</span>
                    <span class="breakdown-value">{{ $data['transactions']['total'] }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Deposits:</span>
                    <span class="breakdown-value">{{ number_format($data['transactions']['deposits'], 2) }}</span>
                </div>
                <div class="breakdown-row">
                    <span class="breakdown-label">Total Withdrawals:</span>
                    <span class="breakdown-value">{{ number_format($data['transactions']['withdrawals'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">Share Bonus Distribution</h2>
        
        @if(count($data['share_bonus']['bonus_details']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Member</th>
                        <th>Savings Balance</th>
                        <th>Proportion (%)</th>
                        <th>Bonus Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['share_bonus']['bonus_details'] as $detail)
                        <tr>
                            <td>{{ $detail['account_number'] }}</td>
                            <td>{{ $detail['member_name'] }}</td>
                            <td>{{ number_format($detail['savings_balance'], 2) }}</td>
                            <td>{{ number_format($detail['proportion'] * 100, 2) }}%</td>
                            <td>{{ number_format($detail['bonus_amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Total Share Bonus:</td>
                        <td>{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-data">
                <p>There are no active savings accounts or the total savings balance is zero for the selected period.</p>
            </div>
        @endif
    </div>
    
    <div class="text-center" style="margin-top: 50px; font-size: 10px; color: #9ca3af;">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>© {{ date('Y') }} MicroLendHub - All rights reserved.</p>
    </div>
</body>
</html>