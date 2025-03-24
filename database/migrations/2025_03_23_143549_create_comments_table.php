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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // ID tự động tăng
            $table->unsignedBigInteger('user_id'); // ID của người bình luận
            $table->unsignedBigInteger('post_id')->nullable(); // ID của bài đăng chủ trọ
            $table->unsignedBigInteger('parent_id')->nullable(); // ID của bình luận cha (nếu là bình luận con)
            $table->text('content'); // Nội dung bình luận
            $table->timestamps(); // created_at và updated_at

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('landlord_posts')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
