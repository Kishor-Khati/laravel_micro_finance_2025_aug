<?php

namespace App\Exports;

use App\Models\SavingsAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SavingsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SavingsAccount::with(['member', 'savingsType'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Account Number',
            'Member',
            'Savings Type',
            'Current Balance',
            'Interest Rate',
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
            $row->account_number,
            $row->member ? $row->member->first_name . ' ' . $row->member->last_name : '',
            $row->savingsType ? $row->savingsType->name : '',
            number_format($row->current_balance, 2),
            $row->interest_rate . '%',
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