<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'portrait',
        'industry',
        'city',
        'bio',
        'contact_phone',
        'contact_email',
        'wechat_qrcode',
        'social_platform',
        'social_url',
        'social_links',
        'is_featured',
        'status',
        'featured_request_status',
        'featured_reason',
        'featured_requested_at',
        'featured_rejected_at',
        'view_count',
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'social_links' => 'array',
        'featured_requested_at' => 'datetime',
        'featured_rejected_at' => 'datetime',
        'view_count' => 'integer',
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
     * 访客浏览记录
     */
    public function views(): HasMany
    {
        return $this->hasMany(EntrepreneurView::class);
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

        // 用显式 ESCAPE 字符兼容 MySQL 与 SQLite 的 LIKE 转义
        // （addcslashes 的 \% 只在 MySQL 生效，SQLite 需 ESCAPE 子句）
        $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);

        return $query->where(function ($q) use ($escaped) {
            $q->whereRaw("name LIKE ? ESCAPE '!'", ["%{$escaped}%"])
              ->orWhereRaw("industry LIKE ? ESCAPE '!'", ["%{$escaped}%"])
              ->orWhereRaw("city LIKE ? ESCAPE '!'", ["%{$escaped}%"]);
        });
    }

    /**
     * 社交平台网址 → 黑色图标文件名（按域名识别；未知域名回退默认 google.svg）
     */
    public static function socialIconForUrl(?string $url): string
    {
        if (!$url) {
            return 'google.svg';
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $map = [
            'abincheung.com' => 'logo.svg',
            'foxfun.cn' => 'logo.svg',
            '61ml.com' => 'logo.svg',
            'voue.cn' => 'logo.svg',
            'vour.cn' => 'logo.svg',
            'ihote.com' => 'logo.svg',
            '61lm.com' => 'logo.svg',
            'xiaohongshu.com' => 'xiaohongshu.svg',
            'weibo.com' => 'weibo.svg',
            'weibo.cn' => 'weibo.svg',
            'douyin.com' => 'douyin.svg',
            'facebook.com' => 'facebook.svg',
            'fb.com' => 'facebook.svg',
            'instagram.com' => 'instagram.svg',
            'telegram.me' => 'telegram.svg',
            't.me' => 'telegram.svg',
            'whatsapp.com' => 'whatsapp.svg',
            'wa.me' => 'whatsapp.svg',
        ];

        foreach ($map as $domain => $file) {
            // 边界匹配：主域本身或子域名（xxx.domain.com）才命中，避免仿冒域名误判
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return $file;
            }
        }

        return 'google.svg';
    }
}
