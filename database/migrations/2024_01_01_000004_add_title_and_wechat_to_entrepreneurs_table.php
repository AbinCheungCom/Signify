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
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->string('title', 100)->nullable()->after('name');
            $table->string('wechat_qrcode')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropColumn(['title', 'wechat_qrcode']);
        });
    }
};
