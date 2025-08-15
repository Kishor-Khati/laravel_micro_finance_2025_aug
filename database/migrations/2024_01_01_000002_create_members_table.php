<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('citizenship_number')->unique();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'kyc_pending'])->default('kyc_pending');
            $table->enum('kyc_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('kyc_documents')->nullable(); // JSON field for document paths
            $table->date('membership_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};