<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrepreneur extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'user_id',
        'name',
        'title',
        'avatar',
        'industry',
        'city',
        'bio',
        'contact_phone',
        'contact_email',
        'wechat_qrcode',
        'is_featured',
        'status',
        'featured_request_status',
        'featured_reason',
        'featured_requested_at',
        'featured_rejected_at',
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'featured_requested_at' => 'datetime',
        'featured_rejected_at' => 'datetime',
    ];

    /**
     * 状态常量
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * 推荐申请状态常量
     */
    const FEATURED_STATUS_PENDING = 'pending';
    const FEATURED_STATUS_APPROVED = 'approved';
    const FEATURED_STATUS_REJECTED = 'rejected';
    const FEATURED_COOLDOWN_DAYS = 15; // 被拒后冷却天数

    /**
     * 获取关联的用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 作用域：仅已认证的记录
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * 作用域：推荐企业家
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * 作用域：待审核的推荐申请
     */
    public function scopeFeaturedPending($query)
    {
        return $query->where('featured_request_status', self::FEATURED_STATUS_PENDING);
    }

    /**
     * 作用域：搜索（防注入）
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        // 转义特殊字符防止 LIKE 注入
        $escaped = addcslashes($search, '%_');

        return $query->where(function ($q) use ($escaped) {
            $q->where('name', 'like', "%{$escaped}%")
              ->orWhere('industry', 'like', "%{$escaped}%")
              ->orWhere('city', 'like', "%{$escaped}%");
        });
    }
}
