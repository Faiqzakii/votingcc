@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-3xl p-8 md:p-12">
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Berikan Suara Anda</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Pilih 3 kandidat terbaik untuk menjadi Change Champion Kalimantan Utara.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-700 font-medium list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('vote.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Choice 1 -->
            <div class="p-6 bg-orange-50/50 rounded-2xl border border-orange-100 transition-all hover:shadow-md hover:border-orange-200">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#f79039] text-white flex items-center justify-center font-bold text-xl shadow-lg">1</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Prioritas Pertama</h3>
                    </div>
                </div>
                <select name="candidate_1" required class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-[#f79039] focus:border-[#f79039] sm:text-sm rounded-xl">
                    <option value="">Pilih Kandidat...</option>
                    @foreach($candidates as $candidate)
                        <option value="{{ $candidate->id }}" {{ old('candidate_1') == $candidate->id ? 'selected' : '' }}>{{ $candidate->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Choice 2 -->
            <div class="p-6 bg-yellow-50/50 rounded-2xl border border-yellow-100 transition-all hover:shadow-md hover:border-yellow-200">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#febd26] text-white flex items-center justify-center font-bold text-xl shadow-lg">2</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Prioritas Kedua</h3>
                    </div>
                </div>
                <select name="candidate_2" required class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-[#febd26] focus:border-[#febd26] sm:text-sm rounded-xl">
                    <option value="">Pilih Kandidat...</option>
                    @foreach($candidates as $candidate)
                        <option value="{{ $candidate->id }}" {{ old('candidate_2') == $candidate->id ? 'selected' : '' }}>{{ $candidate->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Choice 3 -->
            <div class="p-6 bg-yellow-50/30 rounded-2xl border border-yellow-100 transition-all hover:shadow-md hover:border-yellow-200">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#ffd635] text-white flex items-center justify-center font-bold text-xl shadow-lg">3</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Prioritas Ketiga</h3>
                    </div>
                </div>
                <select name="candidate_3" required class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-[#ffd635] focus:border-[#ffd635] sm:text-sm rounded-xl">
                    <option value="">Pilih Kandidat...</option>
                    @foreach($candidates as $candidate)
                        <option value="{{ $candidate->id }}" {{ old('candidate_3') == $candidate->id ? 'selected' : '' }}>{{ $candidate->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-full shadow-lg text-lg font-medium text-white bg-gradient-to-r from-[#f79039] to-[#febd26] hover:from-[#e08030] hover:to-[#e5aa20] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f79039] transform transition-all duration-150 hover:scale-[1.02]">
                    Kirim Suara
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
