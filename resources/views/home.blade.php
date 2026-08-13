@extends('layouts.app')

@section('title', '首页')

@section('content')

<section class="max-w-7xl mx-auto px-6 pt-20 pb-16">
  <p class="label-caption text-accent mb-6">SIGNIFY</p>
  <h1 class="font-display text-display-xl font-black text-ink leading-tight max-w-4xl">
    每一份引领行业的商业远见，<br>都值得被更广泛地看见。
  </h1>
  <p class="mt-6 text-lg text-ink-soft max-w-2xl leading-relaxed">
    不用复杂定义，只用数字化技术，把企业家的个人价值放大成看得见的核心竞争力。
  </p>
</section>

@if($featuredEntrepreneurs->isNotEmpty())
<section class="max-w-7xl mx-auto px-6 pb-24">
  <div class="flex items-center justify-between border-b border-hairline pb-6 mb-12">
    <h2 class="font-display text-display-md font-bold text-ink">本期推荐</h2>
    <a href="{{ route('entrepreneurs.index') }}" class="label-caption text-accent hover:opacity-70 transition-opacity">查看全部 →</a>
  </div>

  <div class="space-y-20">
    @foreach($featuredEntrepreneurs as $i => $entrepreneur)
      <article class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-14 items-center">
        <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}" class="block group {{ $i % 2 === 1 ? 'md:order-2' : '' }}">
          @if($entrepreneur->avatar)
            <img src="{{ asset('storage/'.$entrepreneur->avatar) }}" alt="{{ $entrepreneur->name }}"
                 class="w-full aspect-[4/5] object-cover border border-hairline group-hover:scale-[1.01] transition-transform duration-500">
          @else
            <div class="w-full aspect-[4/5] bg-ink/5 border border-hairline flex items-center justify-center">
              <span class="font-display text-7xl text-ink/20">{{ mb_substr($entrepreneur->name, 0, 1) }}</span>
            </div>
          @endif
        </a>
        <div class="{{ $i % 2 === 1 ? 'md:order-1' : '' }}">
          <p class="label-caption text-muted mb-4">{{ $entrepreneur->industry ?? '—' }} · {{ $entrepreneur->city ?? '—' }}</p>
          <h3 class="font-display text-display-md font-bold text-ink mb-5">
            <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}" class="hover:text-accent transition-colors duration-200">{{ $entrepreneur->name }}</a>
          </h3>
          @if($entrepreneur->bio)
            <p class="text-ink-soft leading-relaxed line-clamp-3">{{ $entrepreneur->bio }}</p>
          @endif
          <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}"
             class="inline-block mt-8 label-caption text-accent hover:opacity-70 transition-opacity">阅读档案 →</a>
        </div>
      </article>
    @endforeach
  </div>
</section>
@endif

@endsection
