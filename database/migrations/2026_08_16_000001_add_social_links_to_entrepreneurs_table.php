<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 新增 social_links JSON 列，并迁移现有单条 social_url 数据。
     * （MySQL 5.7 原生 JSON；SQLite 映射为 TEXT，兼容）
     */
    public function up(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->json('social_links')->nullable()->after('social_url');
        });

        // 现有 social_url → social_links[0]，不丢数据
        foreach (DB::table('entrepreneurs')
            ->whereNotNull('social_url')
            ->where('social_url', '!=', '')
            ->get(['id', 'social_url']) as $row) {
            DB::table('entrepreneurs')->where('id', $row->id)->update([
                'social_links' => json_encode([$row->social_url]),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropColumn('social_links');
        });
    }
};
