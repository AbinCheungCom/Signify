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
        Schema::create('entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('industry')->nullable();
            $table->string('city')->nullable();
            $table->text('bio')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // 索引优化
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
