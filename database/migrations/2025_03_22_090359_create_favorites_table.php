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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->foreignId('post_id')->constrained('landlord_posts')->onDelete('cascade');
            $table->unsignedBigInteger('favoriteable_id'); // ID của bài đăng (landlord/customer post)
            $table->string('favoriteable_type'); // Loại bài đăng (App\Models\LandlordPost hoặc CustomerPost)
            $table->timestamps();

            // Index để tối ưu truy vấn
            $table->index(['favoriteable_id', 'favoriteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
