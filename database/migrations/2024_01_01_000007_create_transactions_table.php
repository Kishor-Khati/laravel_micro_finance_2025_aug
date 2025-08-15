<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'loan_disbursement', 'loan_payment', 'interest_earned', 'fee_charge']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type')->nullable(); // loan, savings_account
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description');
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->date('transaction_date');
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};