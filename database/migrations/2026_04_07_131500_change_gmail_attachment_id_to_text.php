<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE gmail_attachments MODIFY gmail_attachment_id TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE gmail_attachments MODIFY gmail_attachment_id VARCHAR(255) NULL');
    }
};
