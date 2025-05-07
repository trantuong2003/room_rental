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
        Schema::create('landlord_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('price'); // Giá (chuyển từ decimal sang string)
            $table->string('address'); // Địa chỉ chính xác
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('acreage'); // Đổi từ area sang acreage và chuyển kiểu sang string
            $table->integer('bedrooms'); // Số phòng ngủ
            $table->integer('bathrooms'); // Số phòng vệ sinh
            $table->string('electricity_price')->nullable(); // Giá điện (chuyển từ decimal sang string)
            $table->string('internet_price')->nullable(); // Giá internet (chuyển từ decimal sang string)
            $table->string('water_price')->nullable(); // Giá nước (chuyển từ decimal sang string)
            $table->string('service_price')->nullable(); // Giá dịch vụ (chuyển từ decimal sang string)
            $table->string('furniture')->nullable(); // Nội thất
            $table->text('utilities')->nullable(); // Tiện ích
            $table->text('rejection_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_posts');
    }
};
