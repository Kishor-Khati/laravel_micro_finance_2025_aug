<?php

namespace App\Exports;

use App\Models\Loan;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Create a collection with summary data
        $summary = new Collection([
            [
                'type' => 'Members',
                'total' => Member::count(),
                'active' => Member::whereHas('savingsAccounts', function($q) {
                    $q->where('status', 'active');
                })->count(),
                'inactive' => Member::whereDoesntHave('savingsAccounts', function($q) {
                    $q->where('status', 'active');
                })->count(),
            ],
            [
                'type' => 'Loans',
                'total' => Loan::count(),
                'active' => Loan::where('status', 'active')->count(),
                'inactive' => Loan::where('status', '!=', 'active')->count(),
                'amount' => Loan::sum('amount'),
            ],
            [
                'type' => 'Savings',
                'total' => SavingsAccount::count(),
                'active' => SavingsAccount::where('status', 'active')->count(),
                'inactive' => SavingsAccount::where('status', '!=', 'active')->count(),
                'amount' => SavingsAccount::sum('balance'),
            ],
            [
                'type' => 'Transactions',
                'total' => Transaction::count(),
                'deposits' => Transaction::where('type', 'deposit')->count(),
                'withdrawals' => Transaction::where('type', 'withdrawal')->count(),
                'amount' => Transaction::sum('amount'),
            ],
            [
                'type' => 'Expenses',
                'total' => Expense::count(),
                'amount' => Expense::sum('amount'),
            ],
        ]);
        
        return $summary;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Category',
            'Total Count',
            'Active',
            'Inactive',
            'Total Amount',
            'Created At'
        ];
    }

    /**
     * @param array $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['type'],
            $row['total'],
            $row['active'] ?? 'N/A',
            $row['inactive'] ?? 'N/A',
            $row['amount'] ?? 0,
            now()->format('Y-m-d')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            // Style the first column
            'A' => ['font' => ['bold' => true]],
        ];
    }
}