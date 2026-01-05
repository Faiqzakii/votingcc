@extends('layout')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass-card w-full max-w-lg p-8 rounded-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Kandidat</h1>
        </div>

        <form action="{{ route('candidates.update', $candidate) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" value="{{ $candidate->name }}" required class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" required class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ $candidate->description }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">URL Foto</label>
                <input type="url" name="photo_url" value="{{ $candidate->photo_path }}" class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center">
                <input id="is_finalist" name="is_finalist" type="checkbox" value="1" {{ $candidate->is_finalist ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_finalist" class="ml-2 block text-sm text-gray-900">
                    Masuk Final (5 Besar) - Tahap 2
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('candidates.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg">
                    Perbarui Kandidat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
