@extends('admin.layouts.app')

@section('title', 'Пользователи')

@section('content')
<!-- Поиск -->
<div class="card filters-card">
    <form action="{{ route('admin.users') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label>Телефон</label>
            <input type="text" name="phone" value="{{ request('phone') }}" placeholder="+7...">
        </div>
        <button type="submit" class="btn btn-primary">Найти</button>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">Сбросить</a>
    </form>
</div>

<!-- Таблица пользователей -->
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Телефон</th>
                <th>Дата регистрации</th>
                <th>Всего чеков</th>
                <th>Одобрено</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                <td>{{ $user->receipts_count }}</td>
                <td>{{ $user->approved_receipts_count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Пользователей не найдено</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->withQueryString()->links() }}
</div>
@endsection