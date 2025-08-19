<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('share_bonus_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number')->unique(); // Unique record identifier
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('savings_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('bonus_amount', 15, 2);
            $table->decimal('savings_balance_at_calculation', 15, 2); // Balance when bonus was calculated
            $table->decimal('proportion_percentage', 8, 4); // Member's proportion of total savings
            $table->date('calculation_date'); // Date when bonus was calculated
            $table->date('period_start_date'); // Start date of the period for which bonus is calculated
            $table->date('period_end_date'); // End date of the period for which bonus is calculated
            $table->decimal('total_net_income', 15, 2); // Total net income for the period
            $table->decimal('share_bonus_percentage', 5, 2); // Percentage of net income allocated as share bonus
            $table->decimal('total_share_bonus_pool', 15, 2); // Total amount available for distribution
            $table->enum('status', ['calculated', 'applied', 'reversed'])->default('calculated');
            $table->timestamp('applied_at')->nullable(); // When bonus was applied to account
            $table->timestamp('reversed_at')->nullable(); // When bonus was reversed
            $table->foreignId('calculated_by')->constrained('users')->onDelete('cascade'); // Who calculated the bonus
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null'); // Who applied the bonus
            $table->foreignId('reversed_by')->nullable()->constrained('users')->onDelete('set null'); // Who reversed the bonus
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['member_id', 'calculation_date']);
            $table->index(['branch_id', 'calculation_date']);
            $table->index(['status', 'calculation_date']);
            $table->index(['period_start_date', 'period_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_bonus_records');
    }
};
