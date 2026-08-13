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

      @if($entrepreneur->bio)
        <p class="mt-8 text-lg text-ink-soft leading-relaxed whitespace-pre-line">{{ $entrepreneur->bio }}</p>
      @endif

      @if($entrepreneur->contact_email || $entrepreneur->contact_phone)
        <dl class="mt-10 pt-8 border-t border-hairline space-y-4">
          @if($entrepreneur->contact_email)
            <div class="flex items-start gap-6">
              <dt class="label-caption text-muted w-16 flex-shrink-0">邮箱</dt>
              <dd class="text-sm text-ink">{{ $entrepreneur->contact_email }}</dd>
            </div>
          @endif
          @if($entrepreneur->contact_phone)
            <div class="flex items-start gap-6">
              <dt class="label-caption text-muted w-16 flex-shrink-0">电话</dt>
              <dd class="text-sm text-ink">{{ $entrepreneur->contact_phone }}</dd>
            </div>
          @endif
        </dl>
      @endif
    </div>
  </article>
</div>

@endsection
