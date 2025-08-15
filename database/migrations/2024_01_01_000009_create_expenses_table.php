<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('category'); // Office supplies, Utilities, Staff salary, etc.
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('vendor_name')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('receipt_image')->nullable(); // Path to receipt image
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('approval_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};