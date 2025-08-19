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
        Schema::create('share_bonus_statements', function (Blueprint $table) {
            $table->id();
            $table->string('statement_number')->unique(); // Unique statement identifier
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->date('period_start_date'); // Start date of the statement period
            $table->date('period_end_date'); // End date of the statement period
            $table->date('generated_date'); // Date when statement was generated
            $table->decimal('total_raw_income', 15, 2); // Total raw income for the period
            $table->decimal('total_expenses', 15, 2); // Total expenses for the period
            $table->decimal('net_income', 15, 2); // Net income (raw income - expenses)
            $table->decimal('share_bonus_percentage', 5, 2); // Percentage allocated for share bonus
            $table->decimal('total_share_bonus_pool', 15, 2); // Total amount available for distribution
            $table->decimal('total_distributed_amount', 15, 2)->default(0); // Amount actually distributed
            $table->integer('total_eligible_members'); // Number of members eligible for bonus
            $table->integer('total_members_received')->default(0); // Number of members who received bonus
            $table->decimal('total_savings_balance', 15, 2); // Total savings balance used for calculation
            $table->json('financial_summary'); // JSON field for detailed financial breakdown
            $table->enum('status', ['generated', 'partially_applied', 'fully_applied', 'cancelled'])->default('generated');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade'); // Who generated the statement
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['branch_id', 'generated_date']);
            $table->index(['status', 'generated_date']);
            $table->index(['period_start_date', 'period_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_bonus_statements');
    }
};
