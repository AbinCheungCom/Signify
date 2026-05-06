<?php

namespace App\Policies;

use App\Models\Entrepreneur;
use App\Models\User;

class EntrepreneurPolicy
{
    /**
     * 用户是否可以更新自己的企业家档案
     * 核心：user_id 匹配验证
     */
    public function update(User $user, Entrepreneur $entrepreneur): bool
    {
        return $user->id === $entrepreneur->user_id;
    }

    /**
     * 用户是否可以查看某个企业家档案
     * 原则：仅可查看已认证的企业家
     */
    public function view(User $user, Entrepreneur $entrepreneur): bool
    {
        return $entrepreneur->status === Entrepreneur::STATUS_APPROVED;
    }

    /**
     * 用户是否可以删除企业家档案
     * 原则：仅用户本人或管理员可删除
     */
    public function delete(User $user, Entrepreneur $entrepreneur): bool
    {
        return $user->id === $entrepreneur->user_id;
    }

    /**
     * 用户是否可以审批企业家档案
     * 原则：仅管理员可操作
     */
    public function approve(User $user, Entrepreneur $entrepreneur): bool
    {
        return $user->is_admin ?? false;
    }
}
