<x-app-layout>
    <div class="max-w-md mx-auto py-8 px-4">
        <h1 class="text-2xl font-semibold mb-6">Editeaza produs</h1>

        <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            @include('products.form')
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 mt-4">
                Actualizeaza
            </button>
        </form>
    </div>
</x-app-layout>