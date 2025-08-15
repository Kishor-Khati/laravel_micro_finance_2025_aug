<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BranchesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Branch::with(['manager', 'members', 'loans', 'savingsAccounts'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Location',
            'Manager',
            'Staff Count',
            'Member Count',
            'Active Loans',
            'Loan Portfolio',
            'Savings Accounts',
            'Savings Balance',
            'Status',
            'Created At'
        ];
    }

    /**
     * @param Branch $branch
     * @return array
     */
    public function map($branch): array
    {
        return [
            $branch->id,
            $branch->name,
            $branch->location,
            $branch->manager ? $branch->manager->name : 'Not Assigned',
            $branch->staff_count ?? 0,
            $branch->members->count(),
            $branch->loans->where('status', 'active')->count(),
            $branch->loans->sum('amount'),
            $branch->savingsAccounts->count(),
            $branch->savingsAccounts->sum('balance'),
            $branch->status,
            $branch->created_at->format('Y-m-d')
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
        ];
    }
}