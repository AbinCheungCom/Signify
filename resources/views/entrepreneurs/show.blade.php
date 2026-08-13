@extends('layouts.app')

@section('title', $entrepreneur->name)

{{-- 分享卡片：图片用该企业家上传的头像 --}}
@section('og-title', $entrepreneur->name.' — SIGNIFY')
@section('og-description', \Illuminate\Support\Str::limit(trim($entrepreneur->bio ?: $entrepreneur->industry ?: '每一份引领行业的商业远见，都值得被更广泛地看见'), 80))
@section('og-url', route('entrepreneurs.show', $entrepreneur->id))
@section('og-image', $entrepreneur->avatar ? url('storage/'.$entrepreneur->avatar) : asset('android-chrome-512x512.png'))

@section('content')

<div class="max-w-7xl mx-auto px-6 py-16">
  <a href="{{ route('entrepreneurs.index') }}" class="label-caption text-muted hover:text-ink transition-colors">← 返回企业家库</a>

  <article class="mt-10 grid grid-cols-1 md:grid-cols-5 gap-12">
    <div class="md:col-span-2">
      @if($entrepreneur->avatar)
        <img src="{{ asset('storage/'.$entrepreneur->avatar) }}" alt="{{ $entrepreneur->name }}"
             class="w-full aspect-[4/5] object-cover border border-hairline">
      @else
        <div class="w-full aspect-[4/5] bg-ink/5 border border-hairline flex items-center justify-center">
          <span class="font-display text-8xl text-ink/20">{{ mb_substr($entrepreneur->name, 0, 1) }}</span>
        </div>
      @endif
    </div>

    <div class="md:col-span-3">
      @if($entrepreneur->is_featured)
        <span class="bg-ink text-paper text-[11px] uppercase tracking-widest px-2.5 py-1">推荐</span>
      @endif
      <p class="label-caption text-accent mt-4">{{ $entrepreneur->industry ?? '—' }} · {{ $entrepreneur->city ?? '—' }}</p>
      <h1 class="mt-4 font-display text-display-lg font-black text-ink">{{ $entrepreneur->name }}</h1>
      @if($entrepreneur->title)
        <p class="mt-2 text-lg text-ink-soft">{{ $entrepreneur->title }}</p>
      @endif

      @if($entrepreneur->bio)
        <p class="mt-8 text-lg text-ink-soft leading-relaxed whitespace-pre-line">{{ $entrepreneur->bio }}</p>
      @endif

      @if($entrepreneur->wechat_qrcode || $entrepreneur->contact_phone || $entrepreneur->contact_email)
        <div class="mt-10 pt-8 border-t border-hairline flex items-center gap-8" x-data="{ showQr: false }">
          @if($entrepreneur->wechat_qrcode)
            <button type="button" @click="showQr = true"
                    class="text-ink hover:opacity-60 transition-opacity" aria-label="微信" title="微信二维码">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
              </svg>
            </button>
          @endif
          @if($entrepreneur->contact_phone)
            <a href="tel:{{ $entrepreneur->contact_phone }}" class="text-ink hover:opacity-60 transition-opacity"
               aria-label="电话" title="{{ $entrepreneur->contact_phone }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
            </a>
          @endif
          @if($entrepreneur->contact_email)
            <a href="mailto:{{ $entrepreneur->contact_email }}" class="text-ink hover:opacity-60 transition-opacity"
               aria-label="邮箱" title="{{ $entrepreneur->contact_email }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </a>
          @endif
        </div>

        <!-- 微信二维码弹窗 -->
        <div x-show="showQr" x-cloak @keydown.escape.window="showQr = false"
             class="fixed inset-0 z-[300] bg-ink/60 backdrop-blur-sm grid place-items-center p-6"
             @click.self="showQr = false">
          <div class="bg-surface border border-hairline p-8 max-w-xs w-full shadow-float">
            <p class="label-caption text-muted text-center mb-4">微信二维码</p>
            <img src="{{ asset('storage/'.$entrepreneur->wechat_qrcode) }}" alt="{{ $entrepreneur->name }} 的微信二维码"
                 class="w-full h-auto">
            <button @click="showQr = false" class="btn-ink w-full mt-6">关闭</button>
          </div>
        </div>
      @endif
    </div>
  </article>
</div>

@endsection
