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

        // 文件字段由下方单独处理（避免空文件输入把已有图片清空）
        unset($data['avatar'], $data['wechat_qrcode']);

        // 头像：先校验并存储新图，成功后再删旧图（失败不误删）
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $avatarService->store($request->file('avatar'));
            $avatarService->delete($entrepreneur->avatar);
        }

        // 微信二维码：同上
        if ($request->hasFile('wechat_qrcode')) {
            $data['wechat_qrcode'] = $avatarService->store($request->file('wechat_qrcode'), 'qrcodes');
            $avatarService->delete($entrepreneur->wechat_qrcode);
        }

        // 直接 update：文本字段为空时（null）即清空生效
        $entrepreneur->update($data);

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
            'status' => Entrepreneur::STATUS_APPROVED,
        ]);

        return redirect()->route('profile.show')->with('success', '档案创建成功！');
    }
}
