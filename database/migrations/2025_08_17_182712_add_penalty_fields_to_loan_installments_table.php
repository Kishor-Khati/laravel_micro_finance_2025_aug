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
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->decimal('penalty_amount', 15, 2)->default(0)->after('outstanding_amount');
            $table->decimal('penalty_rate', 5, 2)->default(0)->after('penalty_amount'); // Daily penalty rate as percentage
            $table->integer('days_overdue')->default(0)->after('penalty_rate');
            $table->date('penalty_calculated_date')->nullable()->after('days_overdue');
            $table->boolean('penalty_waived')->default(false)->after('penalty_calculated_date');
            $table->text('penalty_remarks')->nullable()->after('penalty_waived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn([
                'penalty_amount',
                'penalty_rate',
                'days_overdue',
                'penalty_calculated_date',
                'penalty_waived',
                'penalty_remarks'
            ]);
        });
    }
};
