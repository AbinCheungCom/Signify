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
     * 作用域：搜索
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('industry', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }
}
