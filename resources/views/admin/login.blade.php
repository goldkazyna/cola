<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ панель — Coca-Cola Promo</title>
    <link rel="stylesheet" href="{{ asset('style/admin.css') }}">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Coca-Cola Promo</h1>
            <h2>Вход в админ панель</h2>

            @if($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group checkbox">
                    <label>
                        <input type="checkbox" name="remember">
                        Запомнить меня
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        </div>
    </div>
</body>
</html>