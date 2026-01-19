@props([
    'column',
    'label',
    'currentSort' => null,
    'currentDirection' => 'asc',
])

@php
    $isActive = $currentSort === $column;
    $newDirection = ($isActive && $currentDirection === 'asc') ? 'desc' : 'asc';

    // Build URL preserving existing query parameters
    $params = request()->query();
    $params['sort'] = $column;
    $params['direction'] = $newDirection;
    $url = request()->url() . '?' . http_build_query($params);
@endphp

<a
    href="{{ $url }}"
    {{ $attributes->merge(['class' => 'group inline-flex items-center gap-1 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hover:text-grey-700 transition-colors']) }}
>
    {{ $label }}
    <span class="flex flex-col">
        @if ($isActive)
            @if ($currentDirection === 'asc')
                <svg class="w-3.5 h-3.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            @else
                <svg class="w-3.5 h-3.5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            @endif
        @else
            <svg class="w-3.5 h-3.5 text-grey-300 group-hover:text-grey-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        @endif
    </span>
</a>
