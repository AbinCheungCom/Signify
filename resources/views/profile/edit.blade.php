@extends('layouts.app')

@section('title', '个人中心')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-16">
  <p class="label-caption text-accent mb-4">MY PROFILE</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">个人中心</h1>

  @if(!$entrepreneur)
    <div class="border border-hairline bg-surface p-10">
      <h2 class="font-display text-display-md font-bold text-ink mb-2">创建企业家档案</h2>
      <p class="text-sm text-muted mb-8">创建后需管理员审核，通过并「推荐」后进入企业家库。</p>
      <form method="POST" action="{{ route('profile.create') }}" class="max-w-md space-y-6">
        @csrf
        <div>
          <label class="label-caption text-muted">姓名</label>
          <input type="text" name="name" value="{{ old('name') }}" required autofocus class="input-line">
          @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-ink">创建档案</button>
      </form>
    </div>
  @else
    <div class="mb-8 border border-hairline bg-surface px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
      <p class="text-sm text-ink-soft">
        @if($entrepreneur->status === 'pending')
          档案已提交，等待管理员审核；通过并「推荐」后进入企业家库。
        @else
          完善资料后可生成个人名片，方便分享。
        @endif
      </p>
      <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}"
         class="label-caption text-accent hover:opacity-70 transition-opacity flex-shrink-0">查看我的名片 →</a>
    </div>
    <div class="mb-8 flex items-center gap-4">
      @if($entrepreneur->status === 'pending')
        <span class="border border-hairline px-3 py-1 text-xs text-status-warning">待审核</span>
      @elseif($entrepreneur->status === 'approved')
        <span class="border border-hairline px-3 py-1 text-xs text-status-success">已通过</span>
      @elseif($entrepreneur->status === 'rejected')
        <span class="border border-hairline px-3 py-1 text-xs text-status-danger">已拒绝</span>
      @endif
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
          class="border border-hairline bg-surface p-10 space-y-8">
      @csrf
      @method('PATCH')

      <div>
        <label class="label-caption text-muted">头像</label>
        <div class="mt-3 flex items-center gap-6">
          @if($entrepreneur->avatar)
            <img src="{{ asset('storage/'.$entrepreneur->avatar) }}" alt="头像"
                 class="w-24 h-24 rounded-full object-cover border border-hairline">
          @endif
          <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" class="text-sm text-ink-soft">
        </div>
        @error('avatar') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="label-caption text-muted">微信二维码</label>
        <div class="mt-3 flex items-center gap-6">
          @if($entrepreneur->wechat_qrcode)
            <img src="{{ asset('storage/'.$entrepreneur->wechat_qrcode) }}" alt="微信二维码"
                 class="w-24 h-24 object-contain border border-hairline">
          @endif
          <input type="file" name="wechat_qrcode" accept="image/jpeg,image/png,image/gif,image/webp" class="text-sm text-ink-soft">
        </div>
        <p class="text-xs text-muted mt-2">名片上点击微信图标可展示此二维码，方便他人扫码添加。</p>
        @error('wechat_qrcode') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="label-caption text-muted">姓名</label>
        <input type="text" name="name" value="{{ old('name', $entrepreneur->name) }}" class="input-line">
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="label-caption text-muted">职务</label>
        <input type="text" name="title" value="{{ old('title', $entrepreneur->title) }}" placeholder="如：创始人 / CEO" class="input-line">
        @error('title') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <label class="label-caption text-muted">行业</label>
          <input type="text" name="industry" value="{{ old('industry', $entrepreneur->industry) }}" class="input-line">
        </div>
        <div>
          <label class="label-caption text-muted">城市</label>
          <input type="text" name="city" value="{{ old('city', $entrepreneur->city) }}" class="input-line">
        </div>
      </div>

      <div>
        <label class="label-caption text-muted">简介</label>
        <textarea name="bio" rows="4" class="input-line resize-none">{{ old('bio', $entrepreneur->bio) }}</textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <label class="label-caption text-muted">联系电话</label>
          <input type="text" name="contact_phone" value="{{ old('contact_phone', $entrepreneur->contact_phone) }}" class="input-line">
        </div>
        <div>
          <label class="label-caption text-muted">联系邮箱</label>
          <input type="email" name="contact_email" value="{{ old('contact_email', $entrepreneur->contact_email) }}" class="input-line">
        </div>
      </div>

      <button type="submit" class="btn-ink">保存修改</button>
    </form>
  @endif
</div>
@endsection
