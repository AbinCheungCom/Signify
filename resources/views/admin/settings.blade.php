@extends('layouts.admin')

@section('title', '系统设置')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
  <p class="label-caption text-accent mb-4">ADMIN · SETTINGS</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">系统设置</h1>

  <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
        class="space-y-8">
    @csrf

    {{-- 基础信息 --}}
    <div class="border border-hairline bg-surface p-10 space-y-6">
      <p class="label-caption text-muted">基础信息</p>
      <div>
        <label class="label-caption text-muted">站点名称</label>
        <input type="text" name="site_name" value="{{ old('site_name', $values['site_name']) }}" required
               class="input-line">
        @error('site_name') <p class="field-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="label-caption text-muted">站点描述</label>
        <textarea name="site_description" rows="3" class="input-line resize-none">{{ old('site_description', $values['site_description']) }}</textarea>
        @error('site_description') <p class="field-error">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- 分享卡片 --}}
    <div class="border border-hairline bg-surface p-10 space-y-6">
      <div>
        <p class="label-caption text-muted">微信 / 社交媒体分享卡片</p>
        <p class="text-xs text-muted mt-2">用于微信等平台分享时的默认标题、描述与图片（企业家名片页优先使用其自身头像与简介）。</p>
      </div>
      <div>
        <label class="label-caption text-muted">分享卡片标题</label>
        <input type="text" name="share_title" value="{{ old('share_title', $values['share_title']) }}" class="input-line">
        @error('share_title') <p class="field-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="label-caption text-muted">分享卡片描述</label>
        <textarea name="share_description" rows="3" class="input-line resize-none">{{ old('share_description', $values['share_description']) }}</textarea>
        @error('share_description') <p class="field-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="label-caption text-muted">分享卡片图片</label>
        <div class="mt-3 flex items-center gap-6">
          @if($values['share_image'])
            <img src="{{ $values['share_image'] }}" alt="分享卡片图片"
                 class="w-24 h-24 object-cover border border-hairline">
          @endif
          <div class="flex-1">
            <input type="url" name="share_image" value="{{ old('share_image', $values['share_image']) }}"
                   placeholder="https://… 或留空使用下方上传" class="input-line">
            <p class="text-xs text-muted mt-2">图片地址（绝对 URL）；或直接上传图片文件（将覆盖上面的地址）。</p>
            <input type="file" name="share_image_file" accept="image/jpeg,image/png,image/gif,image/webp"
                   class="text-sm text-ink-soft mt-3">
          </div>
        </div>
        @error('share_image') <p class="field-error">{{ $message }}</p> @enderror
        @error('share_image_file') <p class="field-error">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Footer --}}
    <div class="border border-hairline bg-surface p-10 space-y-6">
      <p class="label-caption text-muted">Footer</p>
      <div>
        <label class="label-caption text-muted">版权信息</label>
        <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $values['footer_copyright']) }}"
               placeholder="如：© 2026 SIGNIFY" class="input-line">
        @error('footer_copyright') <p class="field-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="label-caption text-muted">ICP 备案号</label>
        <input type="text" name="icp_number" value="{{ old('icp_number', $values['icp_number']) }}"
               placeholder="如：京ICP备12345678号（留空则不展示）" class="input-line">
        @error('icp_number') <p class="field-error">{{ $message }}</p> @enderror
      </div>
    </div>

    <button type="submit" class="btn-ink">保存设置</button>
  </form>
</div>
@endsection
