<x-app-layout>
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Produse</h1>
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                + Adauga
            </a>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-md divide-y">
            @forelse ($products as $product)
                <div class="flex justify-between items-center p-4">
                    <div>
                        <p class="font-medium">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500">
                            SKU: {{ $product->sku }} • {{ $product->unit_measure }} •
                            {{ number_format($product->unit_price, 2) }} RON •
                            {{ $product->is_vat_exempt ? 'Scutit' : $product->vat_rate . '%' }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Sigur stergi acest produs?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="p-4 text-gray-500">Nu exista produse inca.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>