<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SIGNIFY') · 每一份引领行业的商业远见，都值得被更广泛地看见</title>
  <meta name="description" content="不用复杂定义，只用数字化技术，把企业家的个人价值放大成看得见的核心竞争力。">
  <meta name="theme-color" content="#FAFAF7">

  <!-- 浏览器图标 -->
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">
  <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#1A1A18">

  <!-- 微信 / 社交媒体分享（各页可用 og-title / og-description / og-url / og-image 区块覆盖） -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="SIGNIFY">
  <meta property="og:title" content="@yield('og-title', 'SIGNIFY — 每一份引领行业的商业远见，都值得被更广泛地看见')">
  <meta property="og:description" content="@yield('og-description', '不用复杂定义，只用数字化技术，把企业家的个人价值放大成看得见的核心竞争力。')">
  <meta property="og:url" content="@yield('og-url', url('/'))">
  <meta property="og:image" content="@yield('og-image', asset('android-chrome-512x512.png'))">
  <meta property="og:locale" content="zh_CN">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og-title', 'SIGNIFY — 每一份引领行业的商业远见，都值得被更广泛地看见')">
  <meta name="twitter:description" content="@yield('og-description', '不用复杂定义，只用数字化技术，把企业家的个人价值放大成看得见的核心竞争力。')">
  <meta name="twitter:image" content="@yield('og-image', asset('android-chrome-512x512.png'))">

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.cn">
  <link rel="preconnect" href="https://fonts.gstatic.cn" crossorigin>
  <link href="https://fonts.googleapis.cn/css2?family=Playfair+Display:wght@700;900&family=Noto+Serif+SC:wght@700;900&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
  <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body class="min-h-screen flex flex-col">

  <header class="sticky top-0 z-50 bg-paper/85 backdrop-blur-md border-b border-hairline">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-display text-2xl font-black tracking-tight text-ink">SIGNIFY</a>
      <nav class="flex items-center gap-8">
        <a href="{{ route('entrepreneurs.index') }}"
           class="label-caption pb-1 {{ request()->routeIs('entrepreneurs.index', 'entrepreneurs.show') ? 'border-b border-ink text-ink' : 'text-ink-soft hover:text-ink' }}">企业家库</a>
        @auth
          @if(auth()->user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="label-caption text-ink-soft hover:text-ink">后台</a>
          @endif
          <a href="{{ route('profile.show') }}" class="label-caption text-ink-soft hover:text-ink">个人中心</a>
        @else
          <a href="{{ route('login') }}" class="label-caption text-ink-soft hover:text-ink">登录</a>
          <a href="{{ route('register') }}" class="label-caption border border-ink px-4 py-2 hover:bg-ink hover:text-paper transition-colors duration-200">加入</a>
        @endauth
      </nav>
    </div>
  </header>

  <main class="flex-1">
    @include('components.flash')
    @yield('content')
  </main>

  <footer class="border-t border-hairline">
    <div class="max-w-7xl mx-auto px-6 py-12 text-center">
      <p class="label-caption text-muted">SIGNIFY · 每一份引领行业的商业远见，都值得被更广泛地看见</p>
    </div>
  </footer>

</body>
</html>
