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
        'avatar',
        'industry',
        'city',
        'bio',
        'contact_phone',
        'contact_email',
        'is_featured',
        'status',
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /**
     * 状态常量
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
