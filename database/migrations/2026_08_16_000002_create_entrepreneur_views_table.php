<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 访客记录表 + 企业家浏览量缓存列。
     * entrepreneur_views：每次「新访客」计入一条；view_count 为展示用的缓存计数。
     */
    public function up(): void
    {
        Schema::create('entrepreneur_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrepreneur_id')->constrained()->cascadeOnDelete();
            $table->string('session_key', 255)->nullable();
            $table->timestamps();

            $table->index('entrepreneur_id');
        });

        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->after('social_links');
        });
    }

    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
        Schema::dropIfExists('entrepreneur_views');
    }
};
