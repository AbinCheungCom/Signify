<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', '管理后台') · SIGNIFY</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.cn">
  <link rel="preconnect" href="https://fonts.gstatic.cn" crossorigin>
  <link href="https://fonts.googleapis.cn/css2?family=Playfair+Display:wght@700;900&family=Noto+Serif+SC:wght@700;900&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
  <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body class="min-h-screen flex flex-col bg-paper">

  <header class="sticky top-0 z-50 bg-paper/85 backdrop-blur-md border-b border-hairline">
    <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
      <div class="flex items-center gap-8">
        <span class="font-display text-xl font-black tracking-tight text-ink">SIGNIFY</span>
        <nav class="flex items-center gap-6">
          <a href="{{ route('admin.dashboard') }}"
             class="label-caption pb-1 {{ request()->routeIs('admin.dashboard') ? 'border-b border-ink text-ink' : 'text-ink-soft hover:text-ink' }}">待审核</a>
          <a href="{{ route('admin.entrepreneurs') }}"
             class="label-caption pb-1 {{ request()->routeIs('admin.entrepreneurs') ? 'border-b border-ink text-ink' : 'text-ink-soft hover:text-ink' }}">全部企业家</a>
        </nav>
      </div>
      <a href="{{ route('home') }}" class="label-caption text-muted hover:text-ink">返回首页</a>
    </div>
  </header>

  <main class="flex-1">
    @include('components.flash')
    @yield('content')
  </main>

</body>
</html>
