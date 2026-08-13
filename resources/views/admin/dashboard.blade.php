@extends('layouts.admin')

@section('title', '后台首页')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
  <p class="label-caption text-accent mb-4">DASHBOARD</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">待审核申请</h1>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
    @foreach([
      ['label' => '全部企业家', 'value' => $stats['total'], 'color' => 'text-ink'],
      ['label' => '待审核', 'value' => $stats['pending'], 'color' => 'text-status-warning'],
      ['label' => '已通过', 'value' => $stats['approved'], 'color' => 'text-status-success'],
      ['label' => '已拒绝', 'value' => $stats['rejected'], 'color' => 'text-status-danger'],
    ] as $card)
      <div class="border border-hairline bg-surface p-6">
        <div class="font-display text-4xl font-black {{ $card['color'] }} tabular-nums">{{ $card['value'] }}</div>
        <div class="label-caption text-muted mt-2">{{ $card['label'] }}</div>
      </div>
    @endforeach
  </div>

  <div class="border border-hairline bg-surface">
    <div class="px-6 py-4 border-b border-hairline">
      <h2 class="font-display text-lg font-bold text-ink">待审核列表</h2>
    </div>

    @if($pending->isEmpty())
      <div class="py-16 text-center">
        <p class="font-display text-display-md font-bold text-ink">暂无待审核的申请</p>
      </div>
    @else
      <div class="divide-y divide-hairline">
        @foreach($pending as $entrepreneur)
          <div class="px-6 py-5 flex items-center justify-between gap-6">
            <div class="flex items-center gap-5 min-w-0">
              @if($entrepreneur->avatar)
                <img src="{{ asset('storage/'.$entrepreneur->avatar) }}" alt="" class="w-12 h-12 rounded-full object-cover border border-hairline flex-shrink-0">
              @else
                <div class="w-12 h-12 rounded-full bg-ink/5 border border-hairline flex items-center justify-center flex-shrink-0">
                  <span class="font-display text-lg">{{ mb_substr($entrepreneur->name, 0, 1) }}</span>
                </div>
              @endif
              <div class="min-w-0">
                <div class="font-display font-bold text-ink">{{ $entrepreneur->name }}</div>
                <div class="text-sm text-muted mt-0.5">{{ $entrepreneur->industry }} · {{ $entrepreneur->city }}</div>
                @if($entrepreneur->bio)
                  <div class="text-sm text-ink-soft mt-1 truncate max-w-md">{{ $entrepreneur->bio }}</div>
                @endif
              </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
              <form method="POST" action="{{ route('admin.approve', $entrepreneur) }}">
                @csrf
                <button class="btn-ink !py-2 !px-5">通过</button>
              </form>
              <form method="POST" action="{{ route('admin.reject', $entrepreneur) }}">
                @csrf
                <button class="btn-outline !py-2 !px-5">拒绝</button>
              </form>
              <a href="{{ route('entrepreneurs.show', $entrepreneur->id) }}" target="_blank" rel="noopener"
                 class="label-caption text-muted hover:text-ink px-2 transition-colors">查看</a>
            </div>
          </div>
        @endforeach
      </div>
      <div class="px-6 py-4 border-t border-hairline">
        <x-pagination :paginator="$pending" />
      </div>
    @endif
  </div>
</div>
@endsection
