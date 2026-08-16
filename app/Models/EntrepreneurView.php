<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntrepreneurView extends Model
{
    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'entrepreneur_id',
        'session_key',
    ];

    /**
     * 关联的企业家档案
     */
    public function entrepreneur(): BelongsTo
    {
        return $this->belongsTo(Entrepreneur::class);
    }
}
