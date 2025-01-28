<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            if (Schema::hasColumn('bug_reports', 'category')) {
                $table->string('category')->change();
            }
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->string('category')->change();
            $table->string('frequency')->change();
            $table->string('priority')->default('LOW')->change();
            $table->string('status')->default('OPEN')->change();
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->string('priority')->default('LOW')->change();
            $table->string('status')->default('UNDER_REVIEW')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            // Drop the columns first
            $table->dropColumn(['category', 'frequency', 'priority', 'status']);
        });
    
        Schema::table('bug_reports', function (Blueprint $table) {
            // Recreate the columns with ENUM constraints
            $table->enum('category', ['UI', 'Performance', 'Incorrect-Marker', 'Route-Path', 'Other'])->nullable(false);
            $table->enum('frequency', ['Always', 'Sometimes', 'Rarely'])->nullable(false);
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('LOW');
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'])->default('OPEN');
        });
    
        Schema::table('feedback', function (Blueprint $table) {
            // Drop the columns first
            $table->dropColumn(['priority', 'status']);
        });
    
        Schema::table('feedback', function (Blueprint $table) {
            // Recreate the columns with ENUM constraints
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH'])->default('LOW');
            $table->enum('status', ['UNDER_REVIEW', 'IN_PROGRESS', 'IMPLEMENTED', 'CLOSED'])->default('UNDER_REVIEW');
        });
    }
    
};