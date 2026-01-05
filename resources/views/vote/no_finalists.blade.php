@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto text-center min-h-[60vh] flex flex-col justify-center items-center">
    <div class="glass-card rounded-3xl p-12 max-w-lg w-full">
        <div class="mb-6 flex justify-center">
            <div class="h-24 w-24 bg-orange-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Belum Ada Finalis</h1>
        
        <p class="text-gray-600 mb-8 leading-relaxed">
            Tahap 2 belum sepenuhnya siap. Admin belum memilih kandidat yang masuk ke babak final (5 Besar).
            <br>Silakan kembali lagi nanti.
        </p>

        <div class="flex gap-4 justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Keluar
                </button>
            </form>
            
            @if(auth()->user()->username === 'admin')
                <a href="{{ route('candidates.index') }}" class="px-6 py-3 bg-gradient-to-r from-[#f79039] to-[#febd26] text-white font-bold rounded-xl shadow-lg hover:from-[#e08030] hover:to-[#e5aa20] transform transition-all hover:scale-[1.02]">
                    Kelola Finalis
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
