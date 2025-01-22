<?php
// filepath: /C:/Users/kenji/OneDrive/Pictures/clarknav-api/database/migrations/xxxx_xx_xx_xxxxxx_add_is_admin_and_is_user_to_users_table.php
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
            $table->boolean('isAdmin')->default(false);  // Add isAdmin column
            $table->boolean('isUser')->default(true);    // Add isUser column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isAdmin');  // Drop isAdmin column
            $table->dropColumn('isUser');   // Drop isUser column
        });
    }
};