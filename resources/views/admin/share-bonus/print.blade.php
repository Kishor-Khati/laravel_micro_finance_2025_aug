<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Bonus Statement #{{ $statement->statement_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #666;
        }
        
        .statement-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .financial-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        
        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }
        
        .summary-card .amount {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }
        
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .records-table th,
        .records-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .records-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        .records-table td {
            font-size: 11px;
        }
        
        .records-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-applied {
            background: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Statement
    </button>

    <div class="header">
        <h1>MicroLendHub</h1>
        <h2>Share Bonus Statement</h2>
        <p>Statement #{{ $statement->statement_number }}</p>
    </div>

    <div class="statement-info">
        <div class="info-section">
            <h3>Statement Details</h3>
            <div class="info-row">
                <span class="info-label">Statement Number:</span>
                <span>{{ $statement->statement_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Period:</span>
                <span>{{ $statement->period_description }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Branch:</span>
                <span>{{ $statement->branch ? $statement->branch->name : 'All Branches' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $statement->status)) }}</span>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Generation Details</h3>
            <div class="info-row">
                <span class="info-label">Generated Date:</span>
                <span>{{ $statement->generated_date->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated By:</span>
                <span>{{ $statement->generatedBy->name ?? 'System' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Members:</span>
                <span>{{ $records->count() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Print Date:</span>
                <span>{{ now()->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="financial-summary">
        <div class="summary-card">
            <h4>Net Income</h4>
            <div class="amount">{{ $statement->formatted_net_income }}</div>
        </div>
        <div class="summary-card">
            <h4>Share Bonus Pool</h4>
            <div class="amount">{{ $statement->formatted_share_bonus_pool }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Savings</h4>
            <div class="amount">{{ $statement->formatted_total_savings_balance }}</div>
        </div>
        <div class="summary-card">
            <h4>Bonus Percentage</h4>
            <div class="amount">{{ number_format($statement->share_bonus_percentage, 2) }}%</div>
        </div>
    </div>

    <table class="records-table">
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Account Number</th>
                <th class="text-right">Savings Balance</th>
                <th class="text-right">Share %</th>
                <th class="text-right">Bonus Amount</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSavings = 0;
                $totalBonus = 0;
            @endphp
            @foreach($records as $record)
                @php
                    $totalSavings += $record->savings_balance;
                    $totalBonus += $record->share_bonus_amount;
                @endphp
                <tr>
                    <td>{{ $record->member->first_name }} {{ $record->member->last_name }}</td>
                    <td>{{ $record->member->account_number }}</td>
                    <td class="text-right">{{ number_format($record->savings_balance, 2) }}</td>
                    <td class="text-right">{{ number_format($record->share_percentage, 4) }}%</td>
                    <td class="text-right">{{ number_format($record->share_bonus_amount, 2) }}</td>
                    <td class="text-center">
                        @if($record->applied_at)
                            <span class="status-applied">Applied</span>
                        @else
                            <span class="status-pending">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="2">TOTALS</td>
                <td class="text-right">{{ number_format($totalSavings, 2) }}</td>
                <td class="text-right">100.00%</td>
                <td class="text-right">{{ number_format($totalBonus, 2) }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    @if($statement->notes)
        <div class="info-section">
            <h3>Notes</h3>
            <p>{{ $statement->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>This statement was generated automatically by MicroLendHub on {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>For questions or concerns, please contact your branch administrator.</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>