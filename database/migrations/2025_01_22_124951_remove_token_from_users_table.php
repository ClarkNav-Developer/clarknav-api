<?php
// filepath: /C:/Users/kenji/OneDrive/Pictures/clarknav-api/database/migrations/xxxx_xx_xx_xxxxxx_remove_token_from_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');  // Drop the token column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable();  // Add the token column back
        });
    }
};