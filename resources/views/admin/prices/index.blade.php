@extends('layouts.dashboard')

@section('title', 'Prices')
@section('page-title', 'Prices')
@section('page-description', 'Show or hide price categories on the booking form and homepage')

@section('content')
    @if(session('success'))
        <div class="mb-4 p-4 bg-success-100 border border-success-200 text-success-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.prices.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-grey-200 bg-grey-50">
                        <th class="px-4 py-3 text-left font-medium text-grey-700">Category</th>
                        <th class="px-4 py-3 text-left font-medium text-grey-700">Amount</th>
                        <th class="px-4 py-3 text-center font-medium text-grey-700">Visible</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-200">
                    @foreach($prices as $price)
                    <tr class="hover:bg-grey-50">
                        <td class="px-4 py-3">
                            <span class="font-medium text-grey-900">{{ $price->category }}</span>
                            @if($price->description)
                                <span class="block text-xs text-grey-500 mt-0.5">{{ $price->description }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-grey-700">RM {{ number_format($price->amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <input type="hidden" name="prices[{{ $price->id }}][is_active]" value="0">
                            <input type="checkbox" name="prices[{{ $price->id }}][is_active]" value="1"
                                   class="w-4 h-4 rounded border-grey-300 text-primary-600 focus:ring-primary-500"
                                   {{ $price->is_active ? 'checked' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
                Save Changes
            </button>
        </div>
    </form>
@endsection
