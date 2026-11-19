
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow the hospital system to support additional staff roles.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)
                ->default('receptionist')
                ->change();
        });
    }

    /**
     * Restore the original roles if this migration is rolled back.
     */
    public function down(): void
    {
        // Convert superadmin accounts to admin before restoring the enum.
        DB::table('users')
            ->where('role', 'superadmin')
            ->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'doctor',
                'receptionist',
            ])->default('receptionist')->change();
        });
    }
};
