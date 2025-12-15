@extends('admin.layouts.app')

@section('title', 'Чеки')

@section('content')
<!-- Фильтры -->
<div class="card filters-card">
    <form action="{{ route('admin.receipts') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label>Статус</label>
            <select name="status">
                <option value="">Все</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Одобрен</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Отклонён</option>
            </select>
        </div>

        
        <div class="form-group">
            <label>Телефон</label>
            <input type="text" name="phone" value="{{ request('phone') }}" placeholder="+7...">
        </div>

        <button type="submit" class="btn btn-primary">Фильтр</button>
        <a href="{{ route('admin.receipts') }}" class="btn btn-secondary">Сбросить</a>
    </form>
</div>

<!-- Экспорт -->
<div class="card">
    <div class="card-header">
        <h3>Выгрузка для розыгрыша</h3>
    </div>
    <form action="{{ route('admin.receipts.export') }}" method="GET" class="export-form">
        <div class="form-group">
            <label>Период</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="period_type" value="all" checked onchange="toggleDateInputs(this)">
                    За весь период
                </label>
                <label class="radio-label">
                    <input type="radio" name="period_type" value="dates" onchange="toggleDateInputs(this)">
                    По датам
                </label>
            </div>
        </div>
        
        <div class="form-group date-inputs" id="date-inputs" style="display: none;">
            <div class="date-range">
                <div>
                    <label>От</label>
                    <input type="date" name="date_from">
                </div>
                <div>
                    <label>До</label>
                    <input type="date" name="date_to">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="approved_only" value="1" checked>
                Только одобренные
            </label>
        </div>
        
        <button type="submit" class="btn btn-success">Скачать Excel</button>
    </form>
</div>

@push('scripts')
<script>
function toggleDateInputs(radio) {
    const dateInputs = document.getElementById('date-inputs');
    if (radio.value === 'dates') {
        dateInputs.style.display = 'flex';
    } else {
        dateInputs.style.display = 'none';
    }
}
</script>
@endpush

<!-- Таблица чеков -->
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фото</th>
                <th>Телефон</th>
				<th>Фамилия</th>
				<th>Имя</th>
				<th>Город</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $receipt)
            <tr>
                <td>{{ $receipt->id }}</td>
                <td>
                    <img src="{{ Storage::url($receipt->image_path) }}" 
                         alt="Чек" 
                         class="receipt-thumb"
                         onclick="openModal('{{ Storage::url($receipt->image_path) }}')">
                </td>
                <td>{{ $receipt->user->phone }}</td>
				<td>{{ $receipt->user->surname }}</td>
				<td>{{ $receipt->user->name }}</td>
				<td>{{ $receipt->user->city }}</td>
                <td>{{ $receipt->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    @if($receipt->status == 'approved')
                        <span class="badge badge-success">Одобрен</span>
                    @elseif($receipt->status == 'rejected')
                        <span class="badge badge-danger">Отклонён</span>
                        @if($receipt->reject_reason)
                            <br><small>{{ $receipt->reject_reason }}</small>
                        @endif
                    @else
                        <span class="badge badge-warning">На проверке</span>
                    @endif
                </td>
                <td class="actions">
                    @if($receipt->status != 'approved')
                    <form action="{{ route('admin.receipts.approve', $receipt->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">✓</button>
                    </form>
                    @endif

                    @if($receipt->status != 'rejected')
                    <button type="button" class="btn btn-sm btn-warning" onclick="showRejectModal({{ $receipt->id }})">✗</button>
                    @endif

                    <form action="{{ route('admin.receipts.delete', $receipt->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Удалить чек?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">🗑</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Чеков не найдено</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $receipts->withQueryString()->links() }}
</div>

<!-- Модальное окно для просмотра фото -->
<div id="imageModal" class="modal" onclick="closeModal()">
    <span class="modal-close">&times;</span>
    <img id="modalImage" class="modal-content">
</div>

<!-- Модальное окно для отклонения -->
<div id="rejectModal" class="modal">
    <div class="modal-box">
        <h3>Отклонить чек</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Причина отклонения</label>
                <input type="text" name="reason" placeholder="Не соответствует условиям акции">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Отмена</button>
                <button type="submit" class="btn btn-warning">Отклонить</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(src) {
    document.getElementById('imageModal').style.display = 'flex';
    document.getElementById('modalImage').src = src;
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/receipts/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endpush
@endsection