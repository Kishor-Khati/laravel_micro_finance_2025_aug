<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Make citizenship_number nullable
            $table->string('citizenship_number')->nullable()->change();
            
            // Make date_of_birth nullable (already done in previous migration)
            // $table->date('date_of_birth')->nullable()->change();
            
            // Add additional phone number field
            $table->string('phone_secondary')->nullable()->after('phone');
            
            // Add profile image field
            $table->string('profile_image')->nullable()->after('email');
            
            // Add family members field (JSON to store multiple family member IDs)
            $table->json('family_members')->nullable()->after('guardian_relation');
            
            // Add auto-generate flag for member number
            $table->boolean('member_number_auto_generated')->default(true)->after('member_number');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Reverse the changes
            $table->string('citizenship_number')->nullable(false)->change();
            $table->dropColumn([
                'phone_secondary',
                'profile_image', 
                'family_members',
                'member_number_auto_generated'
            ]);
        });
    }
};