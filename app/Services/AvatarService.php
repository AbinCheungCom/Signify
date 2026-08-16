<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AvatarService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * 校验并存储图片，返回存储路径。
     *
     * 服务器无 fileinfo 扩展（ADR-6）：不使用依赖 finfo 的 image/mimes 校验，
     * 改为扩展名白名单 + 可选 GD getimagesize 真实图片校验（function_exists 守卫，
     * 缺 GD 时优雅跳过）。
     *
     * @param string $field 出错时挂载的字段名（头像/形象照/二维码各自独立提示）
     * @throws ValidationException 扩展名或内容非法时
     */
    public function store(UploadedFile $file, string $directory = 'avatars', string $field = 'avatar'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw ValidationException::withMessages([
                $field => '图片仅支持 JPG/PNG/GIF/WEBP 格式',
            ]);
        }

        if (function_exists('getimagesize') && @getimagesize($file->getPathname()) === false) {
            throw ValidationException::withMessages([
                $field => '文件不是有效的图片',
            ]);
        }

        // 使用安全文件名，避免原始文件名注入
        $filename = time().'_'.uniqid().'.'.$extension;

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * 删除旧头像（容错：路径为空则跳过）。
     */
    public function delete(?string $avatar): void
    {
        if ($avatar) {
            Storage::disk('public')->delete($avatar);
        }
    }
}
