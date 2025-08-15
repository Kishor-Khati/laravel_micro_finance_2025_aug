<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Transaction::with(['member', 'savingsAccount', 'loan'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Transaction ID',
            'Member',
            'Type',
            'Amount',
            'Description',
            'Related To',
            'Account/Loan Number',
            'Date',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $relatedTo = '';
        $accountNumber = '';
        
        if ($row->savings_account_id) {
            $relatedTo = 'Savings Account';
            $accountNumber = $row->savingsAccount ? $row->savingsAccount->account_number : '';
        } elseif ($row->loan_id) {
            $relatedTo = 'Loan';
            $accountNumber = $row->loan ? $row->loan->loan_number : '';
        }
        
        return [
            $row->id,
            $row->transaction_id,
            $row->member ? $row->member->first_name . ' ' . $row->member->last_name : '',
            $row->type,
            number_format($row->amount, 2),
            $row->description,
            $relatedTo,
            $accountNumber,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}