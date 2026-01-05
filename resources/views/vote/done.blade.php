@extends('layout')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="glass-card p-10 rounded-3xl max-w-2xl w-full">
        <div class="text-6xl mb-6">🎉</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Terima Kasih!</h1>
        <p class="text-xl text-gray-600 mb-8">Suara Anda telah berhasil direkam. Anda adalah bagian dari perubahan!</p>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-full transition-colors">
                Keluar
            </button>
        </form>
    </div>
</div>
@endsection
