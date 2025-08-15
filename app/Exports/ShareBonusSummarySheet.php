<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ShareBonusSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
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
     * @return string
     */
    public function title(): string
    {
        return 'Summary';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            ['Share Bonus Statement'],
            ['Period: ' . $this->startDate->format('M d, Y') . ' to ' . $this->endDate->format('M d, Y')],
            ['Generated on: ' . Carbon::now()->format('M d, Y H:i')],
            [],
            ['Metric', 'Value']
        ];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = new Collection([
            ['Raw Income (Loan Interest)', number_format($this->data['total_raw_income'], 2)],
            ['Share Bonus (30%)', number_format($this->data['share_bonus']['total_share_bonus'], 2)],
            ['Expenses', number_format($this->data['total_expenses'], 2)],
            ['Net Income', number_format($this->data['final_balance'], 2)],
            [],
            ['Loans Summary', ''],
            ['Total Loans', $this->data['loans']['total_loans']],
            ['Total Loan Amount', number_format($this->data['loans']['total_amount'], 2)],
            ['Total Interest', number_format($this->data['loans']['total_interest'], 2)],
            ['Active Loans', $this->data['loans']['active_loans']],
            ['Active Loan Amount', number_format($this->data['loans']['active_amount'], 2)],
            [],
            ['Savings Summary', ''],
            ['Total Accounts', $this->data['savings']['total_accounts']],
            ['Total Balance', number_format($this->data['savings']['total_balance'], 2)],
            ['Active Accounts', $this->data['savings']['active_accounts']],
            ['Active Balance', number_format($this->data['savings']['active_balance'], 2)],
            [],
            ['Transactions Summary', ''],
            ['Deposits', number_format($this->data['transactions']['deposits'], 2)],
            ['Withdrawals', number_format($this->data['transactions']['withdrawals'], 2)],
            ['Loan Disbursements', number_format($this->data['transactions']['loan_disbursements'], 2)],
            ['Loan Payments', number_format($this->data['transactions']['loan_payments'], 2)],
            ['Total Transactions', $this->data['transactions']['total_transactions']],
        ]);

        return $collection;
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setSize(12);
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet->getStyle('A6:A7')->getFont()->setBold(true);
        $sheet->getStyle('A13:A14')->getFont()->setBold(true);
        $sheet->getStyle('A19:A20')->getFont()->setBold(true);
        
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Add borders to the data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A5:B' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Set column width
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        
        return $sheet;
    }
}