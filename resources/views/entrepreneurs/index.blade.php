@extends('layouts.app')

@section('title', '企业家库')

@section('content')

<section class="max-w-7xl mx-auto px-6 pt-16 pb-10">
  <p class="label-caption text-accent mb-4">THE ENTREPRENEURS</p>
  <h1 class="font-display text-display-lg font-black text-ink">企业家库</h1>
  <p class="mt-4 text-ink-soft max-w-2xl leading-relaxed">探索优秀企业家的形象档案</p>
</section>

<section class="max-w-7xl mx-auto px-6 pb-10">
  <form method="GET" action="{{ route('entrepreneurs.index') }}"
        class="border-b border-hairline pb-6 flex flex-col md:flex-row gap-5 md:items-end">
    <div class="flex-1">
      <label class="label-caption text-muted">搜索</label>
      <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="搜索姓名、行业..."
             class="input-line">
    </div>
    <div class="w-full md:w-48">
      <label class="label-caption text-muted">行业</label>
      <select name="industry" class="input-line">
        <option value="">全部行业</option>
        @foreach($industries as $industry)
          <option value="{{ $industry }}" @selected(($filters['industry'] ?? '') === $industry)>{{ $industry }}</option>
        @endforeach
      </select>
    </div>
    <div class="w-full md:w-48">
      <label class="label-caption text-muted">城市</label>
      <select name="city" class="input-line">
        <option value="">全部城市</option>
        @foreach($cities as $city)
          <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn-ink flex-shrink-0">筛选</button>
  </form>
  <p class="mt-6 text-sm text-muted">共 {{ $entrepreneurs->total() }} 位企业家</p>
</section>

<section class="max-w-7xl mx-auto px-6 pb-6">
  @if($entrepreneurs->isEmpty())
    <div class="py-24 text-center">
      <p class="font-display text-display-md font-bold text-ink">暂无符合条件的记录</p>
      <p class="mt-3 text-muted">试试调整筛选条件</p>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
      @foreach($entrepreneurs as $entrepreneur)
        <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}" class="group block border border-hairline bg-surface">
          <div class="aspect-square overflow-hidden border-b border-hairline">
            @if($entrepreneur->avatar)
              <img src="{{ asset('storage/'.$entrepreneur->avatar) }}" alt="{{ $entrepreneur->name }}"
                   class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
            @else
              <div class="w-full h-full bg-ink/5 flex items-center justify-center">
                <span class="font-display text-6xl text-ink/20">{{ mb_substr($entrepreneur->name, 0, 1) }}</span>
              </div>
            @endif
          </div>
          <div class="p-6">
            <div class="flex items-start justify-between gap-3">
              <h3 class="font-display text-xl font-bold text-ink group-hover:text-accent transition-colors duration-200">{{ $entrepreneur->name }}</h3>
              @if($entrepreneur->is_featured)
                <span class="bg-ink text-paper text-[11px] uppercase tracking-widest px-2.5 py-1 flex-shrink-0">推荐</span>
              @endif
            </div>
            <p class="label-caption text-muted mt-2">{{ $entrepreneur->industry }} · {{ $entrepreneur->city }}</p>
            @if($entrepreneur->bio)
              <p class="mt-3 text-sm text-ink-soft line-clamp-2 leading-relaxed">{{ $entrepreneur->bio }}</p>
            @endif
          </div>
        </a>
      @endforeach
    </div>
  @endif
</section>

<div class="max-w-7xl mx-auto px-6 pb-16">
  <x-pagination :paginator="$entrepreneurs" />
</div>

@endsection
