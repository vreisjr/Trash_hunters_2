@props(['field', 'label', 'sortField', 'sortDirection'])

@php
    $isActive = $field === $sortField;
    $nextDirection = $isActive && $sortDirection === 'asc' ? 'desc' : 'asc';
    $icon = $isActive ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕';
    $query = array_filter([
        'search' => request('search'),
        'status' => request('status'),
        'sort' => $field,
        'direction' => $nextDirection,
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<th class="px-4 py-3 text-left text-sm font-semibold text-zinc-700">
    <a href="{{ request()->url().'?'.http_build_query($query) }}" class="inline-flex items-center gap-2 hover:text-emerald-700">
        {{ $label }}
        <span class="text-xs {{ $isActive ? 'text-emerald-700' : 'text-zinc-400' }}">{{ $icon }}</span>
    </a>
</th>
