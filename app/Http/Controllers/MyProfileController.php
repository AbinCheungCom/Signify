<?php

namespace App\Http\Controllers;

use App\Models\Entrepreneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MyProfileController extends Controller
{
    /**
     * 个人中心 - 展示编辑页面
     */
    public function show()
    {
        $entrepreneur = Auth::user()->entrepreneur;

        return inertia('Profile/Edit', [
            'entrepreneur' => $entrepreneur,
        ]);
    }

    /**
     * 更新个人档案
     * 核心：Policy 验证 user_id 匹配
     */
    public function update(Request $request)
    {
        $entrepreneur = Auth::user()->entrepreneur;

        if (!$entrepreneur) {
            return redirect()->back()->with('error', '您尚未创建企业家档案');
        }

        // Policy 自动验证：仅 user_id 匹配才可更新
        $this->authorize('update', $entrepreneur);

        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'avatar' => [
                'sometimes',
                'file',
                'image',
                'max:2048',
                'mimes:jpg,jpeg,png,gif,webp',
            ],
            'industry' => 'sometimes|string|max:100|nullable',
            'city' => 'sometimes|string|max:100|nullable',
            'bio' => 'sometimes|string|max:500|nullable',
            'contact_phone' => 'sometimes|string|max:20|nullable',
            'contact_email' => 'sometimes|email|max:100|nullable',
        ]);

        // 处理头像上传
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // 验证文件扩展名（防止绕过 MIME 检测）
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->with('error', '头像仅支持 JPG/PNG/GIF/WEBP 格式');
            }

            // 删除旧头像
            if ($entrepreneur->avatar) {
                Storage::disk('public')->delete($entrepreneur->avatar);
            }

            // 保存新头像（使用安全文件名）
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $data['avatar'] = $file->storeAs('avatars', $filename, 'public');
        }

        $entrepreneur->update(array_filter($data, fn($v) => $v !== null));

        return redirect()->back()->with('success', '信息更新成功！');
    }

    /**
     * 创建企业家档案
     */
    public function create(Request $request)
    {
        $existing = Auth::user()->entrepreneur;

        if ($existing) {
            return redirect()->route('profile.show');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $entrepreneur = Entrepreneur::create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'status' => Entrepreneur::STATUS_PENDING,
        ]);

        return redirect()->route('profile.show')->with('success', '档案创建成功，等待审核！');
    }
}
