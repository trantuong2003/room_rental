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
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('commentable_id'); // ID bài đăng
            $table->string('commentable_type'); // Loại bài đăng
            $table->unsignedBigInteger('parent_id')->nullable(); // Cho phép reply comment
            $table->text('content');
            $table->timestamps();
        
            // Khóa ngoại và index
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            $table->index(['commentable_id', 'commentable_type']);
            // $table->id(); // ID tự động tăng
            // $table->unsignedBigInteger('user_id'); // ID của người bình luận
            // $table->unsignedBigInteger('post_id')->nullable(); // ID của bài đăng chủ trọ
            // $table->unsignedBigInteger('parent_id')->nullable(); // ID của bình luận cha (nếu là bình luận con)
            // $table->text('content'); // Nội dung bình luận
            // $table->timestamps(); // created_at và updated_at

            // // Khóa ngoại
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('post_id')->references('id')->on('landlord_posts')->onDelete('cascade');
            // $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
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
