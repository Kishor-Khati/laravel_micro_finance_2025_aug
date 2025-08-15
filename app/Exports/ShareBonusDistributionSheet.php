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

class ShareBonusDistributionSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
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
     * @return string
     */
    public function title(): string
    {
        return 'Share Bonus Distribution';
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            ['Share Bonus Distribution'],
            ['Period: ' . $this->startDate->format('M d, Y') . ' to ' . $this->endDate->format('M d, Y')],
            ['Generated on: ' . Carbon::now()->format('M d, Y H:i')],
            [],
            ['Account Number', 'Member Name', 'Savings Balance', 'Proportion (%)', 'Bonus Amount']
        ];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $rows = [];
        
        foreach ($this->shareBonus['member_bonuses'] as $bonus) {
            $rows[] = [
                $bonus['account_number'],
                $bonus['member_name'],
                number_format($bonus['savings_balance'], 2),
                number_format($bonus['proportion'] * 100, 2) . '%',
                number_format($bonus['bonus_amount'], 2)
            ];
        }
        
        // Add total row
        $rows[] = [
            '',
            'TOTAL',
            '',
            '100%',
            number_format($this->shareBonus['total_distributed'], 2)
        ];
        
        return new Collection($rows);
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setSize(12);
        $sheet->getStyle('A5:E5')->getFont()->setBold(true);
        
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Add borders to the data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A5:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Format the total row
        $sheet->getStyle('A' . $lastRow . ':E' . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastRow . ':E' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        
        return $sheet;
    }
}