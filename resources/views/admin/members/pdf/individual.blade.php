<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Report - {{ $member->first_name }} {{ $member->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
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
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }
        .member-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .member-info h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #2563eb;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            background: #2563eb;
            color: white;
            padding: 10px;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.active {
            background: #d1fae5;
            color: #065f46;
        }
        .status.inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Member Report</h1>
        <h2>{{ $member->first_name }} {{ $member->last_name }}</h2>
        <p>Generated on {{ date('F j, Y') }}</p>
    </div>

    <!-- Member Information -->
    <div class="member-info">
        <h3>Personal Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Member ID:</div>
                <div class="info-value">{{ $member->member_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $member->email ?: 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $member->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $member->address }}, {{ $member->city }}, {{ $member->state }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $member->date_of_birth ? $member->date_of_birth->format('F j, Y') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Gender:</div>
                <div class="info-value">{{ ucfirst($member->gender) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Occupation:</div>
                <div class="info-value">{{ $member->occupation ?: 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Citizenship Number:</div>
                <div class="info-value">{{ $member->citizenship_number ?: 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Branch:</div>
                <div class="info-value">{{ $member->branch ? $member->branch->name : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Member Since:</div>
                <div class="info-value">{{ $member->created_at->format('F j, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status {{ $member->status }}">{{ ucfirst($member->status) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">KYC Status:</div>
                <div class="info-value">
                    <span class="status {{ $member->kyc_status }}">{{ ucfirst($member->kyc_status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Section -->
    <div class="section">
        <h3>Loans ({{ $member->loans->count() }})</h3>
        @if($member->loans->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Loan Number</th>
                        <th>Type</th>
                        <th>Principal</th>
                        <th>Interest Rate</th>
                        <th>Term</th>
                        <th>Monthly Payment</th>
                        <th>Total Payable</th>
                        <th>Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->loans as $loan)
                    <tr>
                        <td>{{ $loan->loan_number }}</td>
                        <td>{{ $loan->loanType ? $loan->loanType->name : 'N/A' }}</td>
                        <td class="text-right">{{ number_format($loan->principal_amount, 2) }}</td>
                        <td class="text-center">{{ $loan->interest_rate }}%</td>
                        <td class="text-center">{{ $loan->term_months }} months</td>
                        <td class="text-right">{{ number_format($loan->monthly_payment, 2) }}</td>
                        <td class="text-right">{{ number_format($loan->total_payable, 2) }}</td>
                        <td class="text-right">{{ number_format($loan->remaining_balance, 2) }}</td>
                        <td class="text-center">
                            <span class="status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No loans found for this member.</div>
        @endif
    </div>

    <!-- Savings Accounts Section -->
    <div class="section">
        <h3>Savings Accounts ({{ $member->savingsAccounts->count() }})</h3>
        @if($member->savingsAccounts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Type</th>
                        <th>Balance</th>
                        <th>Interest Rate</th>
                        <th>Minimum Balance</th>
                        <th>Status</th>
                        <th>Opened Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->savingsAccounts as $account)
                    <tr>
                        <td>{{ $account->account_number }}</td>
                        <td>{{ $account->savingsType ? $account->savingsType->name : 'N/A' }}</td>
                        <td class="text-right">{{ number_format($account->balance, 2) }}</td>
                        <td class="text-center">{{ $account->interest_rate }}%</td>
                        <td class="text-right">{{ number_format($account->minimum_balance, 2) }}</td>
                        <td class="text-center">
                            <span class="status {{ $account->status }}">{{ ucfirst($account->status) }}</span>
                        </td>
                        <td>{{ $account->created_at->format('M j, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No savings accounts found for this member.</div>
        @endif
    </div>

    <!-- Recent Transactions Section -->
    <div class="section">
        <h3>Recent Transactions (Last 10)</h3>
        @if($member->transactions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Balance Before</th>
                        <th>Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->transactions->take(10) as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('M j, Y') }}</td>
                        <td>{{ $transaction->transaction_id }}</td>
                        <td>{{ ucfirst($transaction->transaction_type) }}</td>
                        <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td class="text-right">{{ number_format($transaction->balance_before, 2) }}</td>
                        <td class="text-right">{{ number_format($transaction->balance_after, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No transactions found for this member.</div>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated automatically by the MicroLendHub system.</p>
        <p>Report generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>