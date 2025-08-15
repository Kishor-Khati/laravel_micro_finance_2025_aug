<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class FinanceStatementsExport implements WithMultipleSheets
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new FinanceStatementsSummarySheet($this->data, $this->startDate, $this->endDate),
            new FinanceStatementsShareBonusSheet($this->data['share_bonus'], $this->startDate, $this->endDate),
        ];

        return $sheets;
    }
}

class FinanceStatementsSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $summary = collect([
            [
                'category' => 'Financial Summary',
                'item' => 'Raw Income',
                'value' => $this->data['total_raw_income'],
                'description' => 'Total loan interest earned',
            ],
            [
                'category' => 'Financial Summary',
                'item' => 'Share Bonus',
                'value' => $this->data['share_bonus']['total_share_bonus'],
                'description' => 'Distributed to ' . $this->data['share_bonus']['members_with_savings']->count() . ' members',
            ],
            [
                'category' => 'Financial Summary',
                'item' => 'Expenses',
                'value' => $this->data['total_expenses'],
                'description' => 'Total operational expenses',
            ],
            [
                'category' => 'Financial Summary',
                'item' => 'Net Income',
                'value' => $this->data['final_balance'],
                'description' => 'Raw Income - Share Bonuses - Expenses',
            ],
            [
                'category' => 'Loans',
                'item' => 'Total Loans',
                'value' => $this->data['loans']['total'],
                'description' => 'Number of loans',
            ],
            [
                'category' => 'Loans',
                'item' => 'Total Loan Amount',
                'value' => $this->data['loans']['amount'],
                'description' => 'Total amount disbursed',
            ],
            [
                'category' => 'Loans',
                'item' => 'Active Loans',
                'value' => $this->data['loans']['active'],
                'description' => 'Number of active loans',
            ],
            [
                'category' => 'Savings',
                'item' => 'Total Savings Accounts',
                'value' => $this->data['savings']['total'],
                'description' => 'Number of savings accounts',
            ],
            [
                'category' => 'Savings',
                'item' => 'Total Savings Balance',
                'value' => $this->data['savings']['balance'],
                'description' => 'Total savings balance',
            ],
            [
                'category' => 'Savings',
                'item' => 'Active Savings Accounts',
                'value' => $this->data['savings']['active'],
                'description' => 'Number of active savings accounts',
            ],
            [
                'category' => 'Transactions',
                'item' => 'Total Transactions',
                'value' => $this->data['transactions']['total'],
                'description' => 'Number of transactions',
            ],
            [
                'category' => 'Transactions',
                'item' => 'Total Deposits',
                'value' => $this->data['transactions']['deposits'],
                'description' => 'Total amount deposited',
            ],
            [
                'category' => 'Transactions',
                'item' => 'Total Withdrawals',
                'value' => $this->data['transactions']['withdrawals'],
                'description' => 'Total amount withdrawn',
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
            'Item',
            'Value',
            'Description',
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
            $row['category'],
            $row['item'],
            is_numeric($row['value']) ? number_format($row['value'], 2) : $row['value'],
            $row['description'],
        ];
    }

    /**
     * @param Worksheet $sheet
     *
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            // Add a border to all cells
            'A1:D' . ($this->collection()->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            // Style the header row
            'A1:D1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9E9E9'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Financial Summary';
    }
}

class FinanceStatementsShareBonusSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $shareBonus;
    protected $startDate;
    protected $endDate;

    public function __construct($shareBonus, $startDate, $endDate)
    {
        $this->shareBonus = $shareBonus;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->shareBonus['bonus_details']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Account Number',
            'Member Name',
            'Savings Balance',
            'Proportion (%)',
            'Bonus Amount',
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
            $row['account_number'],
            $row['member_name'],
            number_format($row['savings_balance'], 2),
            number_format($row['proportion'] * 100, 2) . '%',
            number_format($row['bonus_amount'], 2),
        ];
    }

    /**
     * @param Worksheet $sheet
     *
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->collection()->count() + 2; // +1 for header, +1 for total row
        
        // Add total row
        $sheet->setCellValue('A' . $lastRow, 'Total');
        $sheet->setCellValue('E' . $lastRow, number_format($this->shareBonus['total_share_bonus'], 2));
        
        // Merge cells for the total label
        $sheet->mergeCells('A' . $lastRow . ':D' . $lastRow);
        
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            // Add a border to all cells
            'A1:E' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            // Style the header row
            'A1:E1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9E9E9'],
                ],
            ],
            // Style the total row
            'A' . $lastRow . ':E' . $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F5F5F5'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Share Bonus Distribution';
    }
}