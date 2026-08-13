<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MySQL 5.7 兼容：使用 string 类型替代 enum
     */
    public function up(): void
    {
        Schema::create('entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('avatar')->nullable();
            $table->string('industry', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('pending'); // pending/approved/rejected
            $table->timestamps();

            // 索引优化（MySQL 5.7 兼容）
            $table->index('status');
            $table->index('is_featured');
            $table->index(['status', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrepreneurs');
    }
};