@props([
    'route',
    'filters' => [],
    'searchPlaceholder' => 'Search...',
    'searchName' => 'search',
    'showSearch' => true,
])

<form action="{{ $route }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
    {{-- Filter Dropdowns --}}
    @foreach ($filters as $filter)
        <select
            name="{{ $filter['name'] }}"
            class="px-3 py-2 text-sm border border-grey-200 rounded-lg bg-white text-grey-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            onchange="this.form.submit()"
        >
            <option value="">{{ $filter['placeholder'] }}</option>
            @foreach ($filter['options'] as $value => $label)
                <option value="{{ $value }}" {{ request($filter['name']) == (string) $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    @endforeach

    @if ($showSearch)
        {{-- Search Input --}}
        <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <input
                    type="text"
                    name="{{ $searchName }}"
                    value="{{ request($searchName) }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-3 py-2 pl-9 text-sm border border-grey-200 rounded-lg bg-white text-grey-700 placeholder-grey-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-grey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
            >
                Search
            </button>
            @if (request()->hasAny([$searchName, ...array_column($filters, 'name')]))
                <a
                    href="{{ $route }}"
                    class="px-4 py-2 text-sm font-medium text-grey-600 bg-grey-100 rounded-lg hover:bg-grey-200 transition-colors"
                >
                    Clear
                </a>
            @endif
        </div>
    @elseif (request()->hasAny(array_column($filters, 'name')))
        {{-- Clear button when search is hidden but filters are active --}}
        <a
            href="{{ $route }}"
            class="px-4 py-2 text-sm font-medium text-grey-600 bg-grey-100 rounded-lg hover:bg-grey-200 transition-colors"
        >
            Clear
        </a>
    @endif

    {{-- Preserve sort parameters --}}
    @if (request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    @if (request('direction'))
        <input type="hidden" name="direction" value="{{ request('direction') }}">
    @endif
</form>
