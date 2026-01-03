<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Create new beds table with updated status enum
        DB::statement('
            CREATE TABLE beds_new (
                "id" integer primary key autoincrement not null,
                "room_id" integer not null,
                "number" integer not null,
                "status" varchar check ("status" in (\'available\', \'occupied\', \'reserved\', \'under_maintenance\')) not null default \'available\',
                "is_active" tinyint(1) not null default \'1\',
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("room_id") references "rooms"("id") on delete cascade
            )
        ');

        // Copy data from old table, converting needs_cleaning to available
        DB::statement('
            INSERT INTO beds_new (id, room_id, number, status, is_active, created_at, updated_at)
            SELECT
                id,
                room_id,
                number,
                CASE
                    WHEN status = \'needs_cleaning\' THEN \'available\'
                    ELSE status
                END as status,
                is_active,
                created_at,
                updated_at
            FROM beds
        ');

        // Drop old table
        DB::statement('DROP TABLE beds');

        // Rename new table
        DB::statement('ALTER TABLE beds_new RENAME TO beds');

        // Recreate indexes
        DB::statement('CREATE UNIQUE INDEX "beds_room_id_number_unique" on "beds" ("room_id", "number")');
        DB::statement('CREATE INDEX "beds_status_is_active_index" on "beds" ("status", "is_active")');

        DB::statement('PRAGMA foreign_keys = ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: change 'reserved' back to 'needs_cleaning'
        DB::statement('PRAGMA foreign_keys = OFF;');

        DB::statement('
            CREATE TABLE beds_new (
                "id" integer primary key autoincrement not null,
                "room_id" integer not null,
                "number" integer not null,
                "status" varchar check ("status" in (\'available\', \'occupied\', \'needs_cleaning\', \'under_maintenance\')) not null default \'available\',
                "is_active" tinyint(1) not null default \'1\',
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("room_id") references "rooms"("id") on delete cascade
            )
        ');

        DB::statement('
            INSERT INTO beds_new (id, room_id, number, status, is_active, created_at, updated_at)
            SELECT
                id,
                room_id,
                number,
                CASE
                    WHEN status = \'reserved\' THEN \'needs_cleaning\'
                    ELSE status
                END as status,
                is_active,
                created_at,
                updated_at
            FROM beds
        ');

        DB::statement('DROP TABLE beds');
        DB::statement('ALTER TABLE beds_new RENAME TO beds');
        DB::statement('CREATE UNIQUE INDEX "beds_room_id_number_unique" on "beds" ("room_id", "number")');
        DB::statement('CREATE INDEX "beds_status_is_active_index" on "beds" ("status", "is_active")');

        DB::statement('PRAGMA foreign_keys = ON;');
    }
};
