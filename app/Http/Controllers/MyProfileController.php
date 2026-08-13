<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileCreateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Entrepreneur;
use App\Services\AvatarService;
use Illuminate\Support\Facades\Auth;

class MyProfileController extends Controller
{
    /**
     * 个人中心 - 展示编辑页面
     */
    public function show()
    {
        $entrepreneur = Auth::user()->entrepreneur;

        return view('profile.edit', [
            'entrepreneur' => $entrepreneur,
        ]);
    }

    /**
     * 更新个人档案
     * 核心：Policy 验证 user_id 匹配
     */
    public function update(ProfileUpdateRequest $request, AvatarService $avatarService)
    {
        $entrepreneur = Auth::user()->entrepreneur;

        if (!$entrepreneur) {
            return redirect()->back()->with('error', '您尚未创建企业家档案');
        }

        // Policy 自动验证：仅 user_id 匹配才可更新
        $this->authorize('update', $entrepreneur);

        $data = $request->validated();

        // 头像校验与存储抽至 AvatarService（扩展名白名单 + GD 校验，兼容无 fileinfo）
        if ($request->hasFile('avatar')) {
            $avatarService->delete($entrepreneur->avatar);
            $data['avatar'] = $avatarService->store($request->file('avatar'));
        }

        // 微信二维码（同头像校验策略，存 qrcodes 目录）
        if ($request->hasFile('wechat_qrcode')) {
            $avatarService->delete($entrepreneur->wechat_qrcode);
            $data['wechat_qrcode'] = $avatarService->store($request->file('wechat_qrcode'), 'qrcodes');
        }

        $entrepreneur->update(array_filter($data, fn($v) => $v !== null));

        return redirect()->back()->with('success', '信息更新成功！');
    }

    /**
     * 创建企业家档案
     */
    public function create(ProfileCreateRequest $request)
    {
        if (Auth::user()->entrepreneur) {
            return redirect()->route('profile.show');
        }

        $entrepreneur = Entrepreneur::create([
            'user_id' => Auth::id(),
            'name' => $request->validated('name'),
            'status' => Entrepreneur::STATUS_PENDING,
        ]);

        return redirect()->route('profile.show')->with('success', '档案创建成功，等待审核！');
    }
}
