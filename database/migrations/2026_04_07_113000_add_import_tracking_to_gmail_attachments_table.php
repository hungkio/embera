<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gmail_attachments', function (Blueprint $table) {
            $table->timestamp('downloaded_at')->nullable()->after('size');
            $table->timestamp('imported_at')->nullable()->after('downloaded_at');
            $table->string('import_status')->nullable()->after('imported_at');
            $table->text('import_error')->nullable()->after('import_status');
        });
    }

    public function down(): void
    {
        Schema::table('gmail_attachments', function (Blueprint $table) {
            $table->dropColumn([
                'downloaded_at',
                'imported_at',
                'import_status',
                'import_error',
            ]);
        });
    }
};
