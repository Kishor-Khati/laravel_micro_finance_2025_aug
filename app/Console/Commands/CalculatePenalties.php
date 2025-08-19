<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PenaltyService;
use App\Models\LoanInstallment;

class CalculatePenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penalties:calculate {--rate=0.1 : Daily penalty rate percentage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate penalties for overdue loan installments';

    /**
     * Execute the console command.
     */
    public function handle(PenaltyService $penaltyService)
    {
        $this->info('Starting penalty calculation for overdue installments...');
        
        $dailyRate = (float) $this->option('rate');
        
        // Get overdue installments count before calculation
        $overdueCount = LoanInstallment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->where('penalty_waived', false)
            ->count();
            
        if ($overdueCount === 0) {
            $this->info('No overdue installments found.');
            return 0;
        }
        
        $this->info("Found {$overdueCount} overdue installments.");
        $this->info("Using daily penalty rate: {$dailyRate}%");
        
        // Calculate penalties
        $updatedCount = $penaltyService->calculateAllPenalties($dailyRate);
        
        $this->info("Successfully calculated penalties for {$updatedCount} installments.");
        
        // Show statistics
        $statistics = $penaltyService->getPenaltyStatistics();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Overdue Installments', $statistics['total_overdue_installments']],
                ['Total Penalty Amount', 'रू ' . number_format($statistics['total_penalty_amount'], 2)],
                ['Total Waived Penalties', $statistics['total_waived_penalties']],
                ['Average Penalty per Installment', 'रू ' . number_format($statistics['average_penalty_per_installment'], 2)],
            ]
        );
        
        return 0;
    }
}
