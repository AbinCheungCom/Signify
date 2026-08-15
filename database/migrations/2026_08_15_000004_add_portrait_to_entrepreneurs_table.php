<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 形象照（4:5 名片主图）：裁剪时自动派生，与 1:1 头像同源
     */
    public function up(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->string('portrait')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropColumn('portrait');
        });
    }
};
