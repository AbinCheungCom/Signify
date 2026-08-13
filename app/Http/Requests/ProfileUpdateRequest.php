<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 授权校验由 Policy 在控制器层执行。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 头像不做 image/mimes 校验（服务器无 fileinfo），改由 AvatarService 扩展名白名单 + GD 校验。
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'title' => 'sometimes|string|max:100|nullable',
            'avatar' => ['sometimes', 'file', 'max:2048'],
            'industry' => 'sometimes|string|max:100|nullable',
            'city' => 'sometimes|string|max:100|nullable',
            'bio' => 'sometimes|string|max:500|nullable',
            'contact_phone' => 'sometimes|string|max:20|nullable',
            'contact_email' => 'sometimes|email|max:100|nullable',
            'wechat_qrcode' => ['sometimes', 'file', 'max:2048'],
        ];
    }
}
