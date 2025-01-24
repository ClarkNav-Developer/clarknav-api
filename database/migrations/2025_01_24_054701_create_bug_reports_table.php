<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']);
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED']);
            $table->string('device');
            $table->enum('device_os', ['ANDROID', 'IOS', 'WINDOWS', 'MACOS', 'LINUX', 'OTHER']);
            $table->enum('browser', ['CHROME', 'SAFARI', 'FIREFOX', 'EDGE', 'OPERA', 'OTHER']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};