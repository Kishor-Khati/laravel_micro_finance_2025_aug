<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('savings_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('interest_earned', 15, 2)->default(0);
            $table->date('opened_date');
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};