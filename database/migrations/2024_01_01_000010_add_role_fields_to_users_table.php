<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'branch_manager', 'field_officer', 'accountant', 'member'])->default('member');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_id')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['role', 'branch_id', 'employee_id', 'phone', 'status', 'last_login_at']);
        });
    }
};