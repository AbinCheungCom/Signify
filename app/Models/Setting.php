<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * key 为主键，不自增
     */
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'site.settings';

    /**
     * 读取设置。
     * $key 为 null 时返回全量数组；否则返回单值（不存在时返回 $default）。
     * 结果按小时缓存，后台保存时通过 flush() 即时失效。
     * 表不存在时兜底返回默认值，避免迁移未执行导致整站报错。
     */
    public static function get(?string $key = null, $default = null)
    {
        try {
            $all = Cache::remember(self::CACHE_KEY, 3600, function () {
                return static::pluck('value', 'key')->all();
            });
        } catch (\Throwable $e) {
            $all = [];
        }

        return $key === null ? $all : ($all[$key] ?? $default);
    }

    /**
     * 清空设置缓存（保存后调用，保证即时生效）
     */
    public static function flush()
    {
        Cache::forget(self::CACHE_KEY);
    }
}
