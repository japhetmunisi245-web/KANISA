<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite stores Laravel enums as text; MySQL needs its ENUM list expanded.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive','moved','deceased') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('users')->whereIn('status', ['moved', 'deceased'])->update(['status' => 'inactive']);
            DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        }
    }
};
