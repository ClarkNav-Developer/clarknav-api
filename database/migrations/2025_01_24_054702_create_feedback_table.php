<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('comments');
            $table->enum('category', ['FEATURE_SUGGESTION', 'USABILITY_ISSUE', 'APP_PERFORMANCE', 'ROUTE_ACCURACY', 'GENERAL_EXPERIENCE', 'ADDITIONAL_SUGGESTIONS']);
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH']);
            $table->enum('status', ['UNDER_REVIEW', 'IN_PROGRESS', 'IMPLEMENTED', 'CLOSED']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};