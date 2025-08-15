<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2);
            $table->integer('duration_months');
            $table->decimal('monthly_installment', 15, 2)->nullable();
            $table->text('purpose');
            $table->text('collateral')->nullable();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'closed', 'rejected'])->default('pending');
            $table->date('application_date');
            $table->date('approved_date')->nullable();
            $table->date('disbursed_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};