<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gmail_accounts', function (Blueprint $table) {
            $table->timestamp('last_scanned_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('gmail_accounts', function (Blueprint $table) {
            $table->dropColumn('last_scanned_at');
        });
    }
};
