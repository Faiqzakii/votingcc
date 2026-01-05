@extends('layout')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass-card w-full max-w-lg p-8 rounded-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Import Pengguna (Bulk)</h1>
            <p class="text-gray-500 text-sm mt-1">Unggah file CSV untuk menambahkan banyak pengguna sekaligus.</p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r shadow-sm">
            <h3 class="text-sm font-bold text-blue-800 mb-2">Petunjuk:</h3>
            <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                <li>Format file wajib <strong>CSV</strong> (Delimiter: Semicolon <code>;</code>).</li>
                <li>Kolom yang dibutuhkan: <code>name</code>, <code>username</code>.</li>
                <li>Email akan otomatis di-generate: <code>username@bps.go.id</code>.</li>
                <li>Kolom opsional: <code>password</code> (default: 'password').</li>
                <li>Username duplikat akan dilewati.</li>
            </ul>
            <div class="mt-3">
                <a href="{{ route('users.template') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 underline">
                    Download Template CSV
                </a>
            </div>
        </div>

        <form action="{{ route('users.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV</label>
                <input type="file" name="file" required accept=".csv" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100
                    cursor-pointer
                ">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
