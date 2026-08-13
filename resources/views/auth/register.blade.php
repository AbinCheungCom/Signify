@extends('layouts.app')

@section('title', '注册')

@section('content')
<div class="max-w-md mx-auto px-6 py-20">
  <p class="label-caption text-accent mb-4">JOIN</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">注册</h1>

  <form method="POST" action="{{ route('register') }}" class="space-y-8">
    @csrf

    <div>
      <label class="label-caption text-muted">姓名</label>
      <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="input-line">
      @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="label-caption text-muted">邮箱</label>
      <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="input-line">
      @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="label-caption text-muted">密码</label>
      <input type="password" name="password" required autocomplete="new-password" class="input-line">
      @error('password') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="label-caption text-muted">确认密码</label>
      <input type="password" name="password_confirmation" required autocomplete="new-password" class="input-line">
    </div>

    <button type="submit" class="btn-ink w-full">注册</button>
  </form>

  <p class="mt-10 text-sm text-muted text-center">
    已有账号？<a href="{{ route('login') }}" class="text-ink hover:text-accent transition-colors">直接登录</a>
  </p>
</div>
@endsection
