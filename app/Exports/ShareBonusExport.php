<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class ShareBonusExport implements WithMultipleSheets
{
    use Exportable;

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
        $sheets = [];

        // Add summary sheet
        $sheets[] = new ShareBonusSummarySheet($this->data, $this->startDate, $this->endDate);
        
        // Add share bonus distribution sheet
        $sheets[] = new ShareBonusDistributionSheet($this->data['share_bonus'], $this->startDate, $this->endDate);

        return $sheets;
    }
}