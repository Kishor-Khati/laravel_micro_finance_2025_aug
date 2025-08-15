<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoansExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Loan::with(['member', 'loanType'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Loan Number',
            'Member',
            'Loan Type',
            'Principal Amount',
            'Interest Rate',
            'Term (Months)',
            'Total Payable',
            'Total Paid',
            'Remaining Balance',
            'Start Date',
            'End Date',
            'Status',
            'Created Date',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->loan_number,
            $row->member ? $row->member->first_name . ' ' . $row->member->last_name : '',
            $row->loanType ? $row->loanType->name : '',
            number_format($row->principal_amount, 2),
            $row->interest_rate . '%',
            $row->term_months,
            number_format($row->total_payable, 2),
            number_format($row->total_paid, 2),
            number_format($row->remaining_balance, 2),
            $row->start_date ? $row->start_date->format('Y-m-d') : '',
            $row->end_date ? $row->end_date->format('Y-m-d') : '',
            $row->status,
            $row->created_at->format('Y-m-d'),
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