<?php

namespace App\Services;

use App\Models\LoanInstallment;
use App\Models\Loan;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PenaltyService
{
    /**
     * Default daily penalty rate (0.1% per day)
     */
    const DEFAULT_DAILY_PENALTY_RATE = 0.1;

    /**
     * Calculate and update penalties for all overdue installments
     */
    public function calculateAllPenalties(float $dailyPenaltyRate = self::DEFAULT_DAILY_PENALTY_RATE): int
    {
        $overdueInstallments = LoanInstallment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->where('penalty_waived', false)
            ->get();

        $updatedCount = 0;
        foreach ($overdueInstallments as $installment) {
            $installment->updatePenalty($dailyPenaltyRate);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Calculate penalty for a specific installment
     */
    public function calculateInstallmentPenalty(
        LoanInstallment $installment, 
        float $dailyPenaltyRate = self::DEFAULT_DAILY_PENALTY_RATE
    ): float {
        return $installment->calculatePenalty($dailyPenaltyRate);
    }

    /**
     * Get total penalty amount for a loan
     */
    public function getLoanTotalPenalty(Loan $loan): float
    {
        return $loan->installments()->sum('penalty_amount');
    }

    /**
     * Get overdue installments with penalties for a loan
     */
    public function getLoanOverdueInstallments(Loan $loan): Collection
    {
        return $loan->installments()
            ->where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->where('status', 'pending')
                      ->where('due_date', '<', now());
            })
            ->get();
    }

    /**
     * Waive penalty for an installment
     */
    public function waivePenalty(LoanInstallment $installment, string $reason = null): bool
    {
        try {
            $installment->waivePenalty($reason);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Waive penalties for all installments of a loan
     */
    public function waiveLoanPenalties(Loan $loan, string $reason = null): int
    {
        $overdueInstallments = $this->getLoanOverdueInstallments($loan);
        $waivedCount = 0;

        foreach ($overdueInstallments as $installment) {
            if ($this->waivePenalty($installment, $reason)) {
                $waivedCount++;
            }
        }

        return $waivedCount;
    }

    /**
     * Get penalty statistics
     */
    public function getPenaltyStatistics(): array
    {
        $totalOverdueInstallments = LoanInstallment::where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->where('status', 'pending')
                      ->where('due_date', '<', now());
            })
            ->count();

        $totalPenaltyAmount = LoanInstallment::sum('penalty_amount');
        $totalWaivedPenalties = LoanInstallment::where('penalty_waived', true)->count();

        return [
            'total_overdue_installments' => $totalOverdueInstallments,
            'total_penalty_amount' => $totalPenaltyAmount,
            'total_waived_penalties' => $totalWaivedPenalties,
            'average_penalty_per_installment' => $totalOverdueInstallments > 0 
                ? round($totalPenaltyAmount / $totalOverdueInstallments, 2) 
                : 0
        ];
    }

    /**
     * Update penalty rate for future calculations
     */
    public function updatePenaltyRate(float $newRate): bool
    {
        try {
            // Update pending overdue installments with new rate
            LoanInstallment::where('status', 'overdue')
                ->where('penalty_waived', false)
                ->update(['penalty_rate' => $newRate]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}