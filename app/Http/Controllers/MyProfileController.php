<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileCreateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Entrepreneur;
use App\Services\AvatarService;
use Illuminate\Http\Request;
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
            'cities' => config('cities', []),
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
        unset($data['avatar'], $data['wechat_qrcode'], $data['portrait']);

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

        // 形象照（4:5 名片主图）：裁剪时与头像同源自动派生
        if ($request->hasFile('portrait')) {
            $data['portrait'] = $avatarService->store($request->file('portrait'), 'portraits');
            $avatarService->delete($entrepreneur->portrait);
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

    /**
     * 发起推荐申请
     * 需填写申请理由；被拒后 15 天冷却期内不可再申请
     */
    public function requestFeatured(Request $request)
    {
        $entrepreneur = Auth::user()->entrepreneur;

        if (!$entrepreneur) {
            return redirect()->back()->with('error', '您尚未创建企业家档案');
        }

        $this->authorize('requestFeatured', $entrepreneur);

        // 认证门槛：仅已通过认证的档案可申请推荐
        if ($entrepreneur->status !== Entrepreneur::STATUS_APPROVED) {
            return redirect()->back()->with('error', '档案通过认证后方可申请推荐');
        }
        if ($entrepreneur->is_featured) {
            return redirect()->back()->with('error', '您已是推荐企业家');
        }
        if ($entrepreneur->featured_request_status === Entrepreneur::FEATURED_STATUS_PENDING) {
            return redirect()->back()->with('error', '您的推荐申请正在审核中');
        }

        // 冷却期：被拒后 15 天内不可再申请
        $cooldownUntil = $entrepreneur->featured_rejected_at?->addDays(Entrepreneur::FEATURED_COOLDOWN_DAYS);
        if ($cooldownUntil && $cooldownUntil->isFuture()) {
            $days = $cooldownUntil->diffInDays(now()) + 1;
            return redirect()->back()->with('error', "推荐申请被拒后需等待 {$days} 天后再次申请");
        }

        // 申请理由必填
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $entrepreneur->update([
            'featured_request_status' => Entrepreneur::FEATURED_STATUS_PENDING,
            'featured_requested_at'   => now(),
            'featured_reason'         => $data['reason'],
            'featured_rejected_at'    => null,
        ]);

        return redirect()->back()->with('success', '推荐申请已提交，请等待管理员审核');
    }
}
