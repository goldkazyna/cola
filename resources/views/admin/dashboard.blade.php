@extends('admin.layouts.app')

@section('title', 'Статистика')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_users'] }}</div>
        <div class="stat-label">Пользователей</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_receipts'] }}</div>
        <div class="stat-label">Всего чеков</div>
    </div>
    <div class="stat-card success">
        <div class="stat-number">{{ $stats['approved_receipts'] }}</div>
        <div class="stat-label">Одобрено</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-number">{{ $stats['rejected_receipts'] }}</div>
        <div class="stat-label">Отклонено</div>
    </div>
</div>

<div class="card">
    <h3>Периоды розыгрышей</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Период приёма чеков</th>
                <th>Дата розыгрыша</th>
            </tr>
        </thead>
        <tbody>
            @foreach($periods as $period)
            <tr>
                <td>{{ $period['name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($period['start'])->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($period['end'])->format('d.m.Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($period['drawing_date'])->format('d.m.Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection