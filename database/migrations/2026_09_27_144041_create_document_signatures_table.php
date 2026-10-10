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
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('signature_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('page_number');
            $table->decimal('pos_x', 6, 4);
            $table->decimal('pos_y', 6, 4);
            $table->decimal('box_width', 6, 4);
            $table->decimal('box_height', 6, 4);
            $table->timestamp('signed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
    }
};
