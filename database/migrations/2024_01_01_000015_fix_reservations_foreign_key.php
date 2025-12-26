<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite requires recreating the table to fix foreign keys
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Create new table with correct foreign key
        DB::statement('
            CREATE TABLE IF NOT EXISTS "reservations_new" (
                "id" integer primary key autoincrement not null,
                "admission_type_id" integer not null,
                "personnel_id" integer,
                "guest_id" integer,
                "room_id" integer not null,
                "check_in_date" date not null,
                "check_out_date" date not null,
                "actual_check_in" datetime,
                "actual_check_out" datetime,
                "status" varchar check ("status" in (\'pending\', \'confirmed\', \'checked_in\', \'checked_out\', \'cancelled\')) not null default \'pending\',
                "notes" text,
                "created_by" integer not null,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("admission_type_id") references "admission_types"("id"),
                foreign key("personnel_id") references "personnel"("id") on delete set null,
                foreign key("guest_id") references "guests"("id") on delete set null,
                foreign key("room_id") references "rooms"("id"),
                foreign key("created_by") references "users"("id")
            )
        ');

        // Copy data from old table
        DB::statement('
            INSERT INTO reservations_new
            SELECT * FROM reservations
        ');

        // Drop old table
        DB::statement('DROP TABLE reservations');

        // Rename new table
        DB::statement('ALTER TABLE reservations_new RENAME TO reservations');

        // Recreate index
        DB::statement('CREATE INDEX "reservations_status_check_in_date_check_out_date_index" on "reservations" ("status", "check_in_date", "check_out_date")');

        DB::statement('PRAGMA foreign_keys = ON;');
    }

    public function down(): void
    {
        // No rollback needed
    }
};
