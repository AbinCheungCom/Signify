<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 推荐申请：状态 / 理由 / 申请时间 / 拒绝时间（冷却期依据）
     * MySQL 5.7 兼容：使用 string 类型替代 enum
     */
    public function up(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->string('featured_request_status', 20)->nullable()->after('is_featured'); // pending/approved/rejected
            $table->text('featured_reason')->nullable()->after('featured_request_status');   // 申请理由（必填）
            $table->timestamp('featured_requested_at')->nullable()->after('featured_reason');
            $table->timestamp('featured_rejected_at')->nullable()->after('featured_requested_at'); // 拒绝时间（冷却期依据）
            $table->index('featured_request_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            $table->dropIndex(['featured_request_status']);
            $table->dropColumn(['featured_request_status', 'featured_reason', 'featured_requested_at', 'featured_rejected_at']);
        });
    }
};
