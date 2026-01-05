@extends('layout')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass-card w-full max-w-lg p-8 rounded-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Buat Kandidat Baru</h1>
        </div>

        <form action="{{ route('candidates.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" required class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi (Visi/Misi)</label>
                <textarea name="description" rows="3" required class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">URL Foto (Opsional)</label>
                <input type="url" name="photo_url" placeholder="https://..." class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk otomatis membuat avatar.</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('candidates.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg">
                    Buat Kandidat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
