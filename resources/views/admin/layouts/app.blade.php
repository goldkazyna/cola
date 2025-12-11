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
	<style>
	/* Radio и Checkbox группы */
.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 5px;
}

.radio-label,
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
}

.radio-label input,
.checkbox-label input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #910101;
}

/* Диапазон дат */
.date-range {
    display: flex;
    gap: 15px;
}

.date-range > div {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.date-range input[type="date"] {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.date-inputs {
    display: flex;
    align-items: flex-end;
}

/* Обновляем export-form */
.export-form {
    display: flex;
    gap: 20px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.export-form .form-group {
    margin-bottom: 0;
}
	</style>
    <script src="{{ asset('script/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>