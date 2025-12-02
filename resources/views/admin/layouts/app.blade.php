<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ панель') — Coca-Cola Promo</title>
    <link rel="stylesheet" href="{{ asset('style/admin.css') }}">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Сайдбар -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Coca-Cola</h2>
                <span>Админ панель</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    📊 Статистика
                </a>
                <a href="{{ route('admin.receipts') }}" class="{{ request()->routeIs('admin.receipts*') ? 'active' : '' }}">
                    🧾 Чеки
                </a>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    👥 Пользователи
                </a>
            </nav>

            <div class="sidebar-footer">
                <span>{{ Auth::guard('admin')->user()->name }}</span>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit">Выйти</button>
                </form>
            </div>
        </aside>

        <!-- Основной контент -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>@yield('title', 'Админ панель')</h1>
            </header>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('script/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>