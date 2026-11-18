{{-- Flash messages: success / error / warning / info + validation summary --}}
@foreach (['success' => 'check-circle-fill', 'error' => 'exclamation-octagon-fill', 'warning' => 'exclamation-triangle-fill', 'info' => 'info-circle-fill'] as $type => $icon)
    @if (session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show d-flex align-items-center {{ $type === 'success' ? 'auto-dismiss' : '' }}" role="alert">
            <i class="bi bi-{{ $icon }} me-2 fs-5"></i>
            <div>{{ session($type) }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center mb-1">
            <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
            <strong>Please fix the following {{ $errors->count() === 1 ? 'error' : $errors->count().' errors' }}:</strong>
        </div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
