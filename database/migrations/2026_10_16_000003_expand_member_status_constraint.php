<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite stores Laravel enums as text; MySQL needs its ENUM list expanded; PostgreSQL needs check constraint updated.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive','moved','deceased') NOT NULL DEFAULT 'active'");
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'inactive', 'moved', 'deceased'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('users')->whereIn('status', ['moved', 'deceased'])->update(['status' => 'inactive']);
            DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'inactive'))");
        }
    }
};
