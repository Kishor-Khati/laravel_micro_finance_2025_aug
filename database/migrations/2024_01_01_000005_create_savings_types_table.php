<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('min_balance', 15, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0); // Annual interest rate
            $table->integer('withdrawal_limit_per_month')->nullable();
            $table->decimal('withdrawal_limit_amount', 15, 2)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_types');
    }
};