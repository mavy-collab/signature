<?php

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
        Schema::table('signature_audit_logs', function (Blueprint $table) {
            $table->timestamp('created_at', 6)->useCurrent()->change();
        });

        Schema::table('document_audit_logs', function (Blueprint $table) {
            $table->timestamp('created_at', 6)->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signature_audit_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->change();
        });

        Schema::table('document_audit_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->change();
        });
    }
};
