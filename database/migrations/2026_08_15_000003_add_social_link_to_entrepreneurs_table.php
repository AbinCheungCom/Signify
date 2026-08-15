<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 社交媒体链接：平台名（可自由输入）+ 平台主页网址
     */
    public function up(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->string('social_platform', 50)->nullable()->after('contact_email');
            $table->string('social_url', 500)->nullable()->after('social_platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropColumn(['social_platform', 'social_url']);
        });
    }
};
