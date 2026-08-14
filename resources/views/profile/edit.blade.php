@extends('layouts.app')

@section('title', '个人中心')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-16">
  <p class="label-caption text-accent mb-4">MY PROFILE</p>
  <h1 class="font-display text-display-lg font-black text-ink mb-10">个人中心</h1>

  @if(!$entrepreneur)
    <div class="border border-hairline bg-surface p-10">
      <h2 class="font-display text-display-md font-bold text-ink mb-2">创建企业家档案</h2>
      <p class="text-sm text-muted mb-8">创建后自动通过，获「推荐」后进入智库。</p>
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
          档案已提交；获「推荐」后进入智库。
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
          <label class="label-caption text-muted">研究领域</label>
          <input type="text" name="industry" value="{{ old('industry', $entrepreneur->industry) }}" class="input-line">
        </div>
        <div>
          <label class="label-caption text-muted">城市</label>
          <div class="relative" x-data="cityPicker(@js($cities), @js(old('city', $entrepreneur->city)))"
               @keydown.escape.window="open = false" @click.outside="open = false">
            <div class="flex items-end gap-2">
              <input type="text" name="city" id="city-input" x-model="query"
                     autocomplete="off" placeholder="选择或输入城市" @focus="openPicker()" class="input-line">
              <button type="button" id="locate-btn"
                      class="label-caption text-accent hover:opacity-70 transition-opacity flex-shrink-0 pb-2.5 inline-flex items-center gap-1" title="定位到当前城市">
                <svg viewBox="0 0 1024 1024" fill="currentColor" aria-hidden="true" class="w-3.5 h-3.5">
                  <path d="M905.54112 557.370751l-48.570034 83.462918q-2.638505 3.715446-5.384704 6.838574L581.974233 989.708666q-22.131135 28.05431-57.508642 33.277473-35.323661 5.169316-64.616453-15.400255l-0.053847-0.053847-3.769293-2.746199-0.107694-0.107694q-8.346292-6.623186-14.915631-14.969478l-269.719843-342.144117-0.161541-0.215388q-2.692352-3.015434-5.007775-6.353951l-0.107694-0.107694-2.153882-3.230823-1.992341-3.230822-4.523151-6.300105Q89.381476 530.931852 86.635277 413.545297L86.527583 404.283605q0.753859-168.325859 125.517459-286.466273Q336.431714 0 512.350006 0q175.702904 0 300.089575 118.086567Q937.310876 236.711605 937.310876 405.145158q-0.107694 10.230938-0.700012 20.192641l-0.53847 7.538586q-5.061622 65.908782-30.477427 124.494366z m-130.41754 25.038875l3.82314-5.923175 0.215388-0.269235q56.539396-76.624343 57.616337-171.664376 0-125.625153-94.609256-214.634316-95.040032-89.386093-229.819183-89.386093-135.048386 0-230.142266 89.278399-94.555409 88.847622-95.147726 214.795857 0 91.970751 57.777878 173.925952l0.376929-0.269235 2.530811 4.038528 1.184635 1.346176 262.558186 333.15166 262.612033-333.205507 1.076941-1.184635z m-129.771376-37.531389q55.139373-55.19322 55.139373-133.217587 0-78.078214-55.19322-133.271433-55.19322-55.19322-133.271433-55.19322-78.078214 0-133.271434 55.19322-55.19322 55.19322-55.19322 133.271433 0 78.078214 55.19322 133.217587 55.247067 55.247067 133.271434 55.247067 78.078214 0 133.271433-55.247067zM449.833588 473.853986q-25.792734-25.738887-25.792734-62.139489 0-36.454449 25.738887-62.193335 25.792734-25.792734 62.193335-25.792734 36.454449 0 62.193336 25.792734 25.792734 25.738887 25.792734 62.193335 0 36.400602-25.792734 62.139489-25.738887 25.792734-62.193336 25.792734-36.400602 0-62.193335-25.792734z"/>
                </svg>
                <span id="locate-label">定位</span>
              </button>
            </div>

            {{-- 桌面端：输入框下方下拉面板 --}}
            <div x-show="open" x-cloak
                 class="hidden md:block absolute left-0 right-0 mt-1 z-40 max-h-80 overflow-y-auto border border-hairline bg-surface shadow-float">
              <template x-for="c in filtered" :key="c">
                <button type="button" @click="select(c)"
                        class="block w-full text-left px-4 py-2 text-sm hover:bg-paper"
                        :class="c === query ? 'text-accent' : 'text-ink-soft'" x-text="c"></button>
              </template>
              <p x-show="filtered.length === 0" class="px-4 py-3 text-xs text-muted">无匹配城市</p>
            </div>

            {{-- 手机端：全屏城市选择 --}}
            <div x-show="open" x-cloak
                 class="md:hidden fixed inset-0 z-[300] bg-surface flex flex-col">
              <div class="flex items-center justify-between px-6 py-4 border-b border-hairline">
                <p class="font-display text-display-md font-bold text-ink">选择城市</p>
                <button type="button" @click="open = false" class="label-caption text-accent">关闭</button>
              </div>
              <div class="px-6 py-4">
                <input type="search" x-model="query" x-ref="searchInput" placeholder="搜索城市" autofocus class="input-line">
              </div>
              <div class="flex-1 overflow-y-auto px-2 pb-10">
                <template x-for="c in filtered" :key="c">
                  <button type="button" @click="select(c)"
                          class="block w-full text-left px-4 py-3 text-base border-b border-hairline hover:bg-paper"
                          :class="c === query ? 'text-accent' : 'text-ink'" x-text="c"></button>
                </template>
                <p x-show="filtered.length === 0" class="px-4 py-3 text-xs text-muted">无匹配城市</p>
              </div>
            </div>
          </div>
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
<script>
  window.cityPicker = function (cities, initial) {
    return {
      open: false,
      query: (initial || ''),
      cities: cities || [],
      get filtered() {
        var q = this.query.trim();
        if (!q) return this.cities;
        return this.cities.filter(function (c) { return c.indexOf(q) !== -1; });
      },
      openPicker: function () {
        this.open = true;
        var self = this;
        this.$nextTick(function () {
          if (self.$refs.searchInput && window.matchMedia('(max-width: 767px)').matches) {
            self.$refs.searchInput.focus();
          }
        });
      },
      select: function (c) {
        this.query = c;
        this.open = false;
        if (document.activeElement && document.activeElement.blur) document.activeElement.blur();
      }
    };
  };
</script>
@endsection
