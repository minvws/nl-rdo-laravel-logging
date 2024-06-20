<?php

declare(strict_types=1);

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
        Schema::table('audit_logs', static function (Blueprint $table) {
            $table->renameColumn('context', 'request');
            $table->renameColumn('pii_context', 'pii_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', static function (Blueprint $table) {
            $table->renameColumn('request', 'context');
            $table->renameColumn('pii_request', 'pii_context');
        });
    }
};
