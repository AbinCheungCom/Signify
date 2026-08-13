@extends('layouts.app')

@section('title', '重置密码')

@section('content')
<div class="max-w-md mx-auto px-6 py-20">
  <p class="label-caption text-accent mb-4">RESET PASSWORD</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">重置密码</h1>

  <form method="POST" action="{{ route('password.store') }}" class="space-y-8">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div>
      <label class="label-caption text-muted">邮箱</label>
      <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus autocomplete="username" class="input-line">
      @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="label-caption text-muted">新密码</label>
      <input type="password" name="password" required autocomplete="new-password" class="input-line">
      @error('password') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="label-caption text-muted">确认密码</label>
      <input type="password" name="password_confirmation" required autocomplete="new-password" class="input-line">
    </div>

    <button type="submit" class="btn-ink w-full">重置密码</button>
  </form>
</div>
@endsection
