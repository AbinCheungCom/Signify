<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SIGNIFY') · 企业家形象资产</title>
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
      <p class="label-caption text-muted">SIGNIFY · 企业家形象资产数字化系统</p>
    </div>
  </footer>

</body>
</html>
