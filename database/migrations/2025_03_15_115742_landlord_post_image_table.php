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
        Schema::create('landlord_post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_post_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // Đường dẫn ảnh
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_post_images');
    }
};
