@extends('layout')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Dashboard</h1>
            <p class="text-orange-100">Pantauan hasil langsung</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-4 mb-2">
                <!-- Phase Switcher (Admin Only) -->
                <form action="{{ route('dashboard.update-phase') }}" method="POST" class="inline-flex items-center bg-white/80 rounded-lg px-3 py-1.5 shadow-sm border border-orange-100">
                    @csrf
                    <span class="text-xs font-bold text-gray-600 mr-2 uppercase tracking-wide">Status Voting:</span>
                    <div class="flex bg-gray-100 rounded p-1">
                        <button type="submit" name="phase" value="1" class="px-3 py-1 text-xs font-bold rounded {{ $currentPhase == 1 ? 'bg-[#f79039] text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            Tahap 1
                        </button>
                        <button type="submit" name="phase" value="2" class="px-3 py-1 text-xs font-bold rounded {{ $currentPhase == 2 ? 'bg-[#f79039] text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            Tahap 2
                        </button>
                        <button type="submit" name="phase" value="3" class="px-3 py-1 text-xs font-bold rounded {{ $currentPhase == 3 ? 'bg-red-600 text-white shadow-sm' : 'text-gray-500 hover:text-red-700' }}" onclick="return confirm('Apakah Anda yakin ingin MENUTUP sesi voting? Pengguna tidak akan bisa memilih lagi.')">
                            Tutup
                        </button>
                    </div>
                </form>

                <form action="{{ route('dashboard.dummy-votes') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin generate dummy votes untuk SEMUA pengguna yang belum memilih?');">
                   @csrf
                   <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 text-xs font-bold rounded-lg border border-red-200">
                       Auto Vote (Dummy)
                   </button>
                </form>

                <div class="glass-card px-4 py-2 rounded-lg text-sm font-medium text-orange-900 bg-white/80 h-[42px] flex items-center">
                    Terakhir diperbarui: {{ now()->timezone('Asia/Makassar')->format('H:i:s') }} WITA
                </div>
            </div>

            <div class="inline-flex rounded-md shadow-sm" role="group">
                <a href="{{ route('dashboard', ['phase' => 1]) }}" class="px-4 py-2 text-sm font-medium {{ $phase == 1 ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-200 rounded-l-lg">
                    Tahap 1
                </a>
                <a href="{{ route('dashboard', ['phase' => 2]) }}" class="px-4 py-2 text-sm font-medium {{ $phase == 2 ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-200 rounded-r-lg border-l-0">
                    Tahap 2
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Total Suara Masuk</h3>
            <div class="flex items-baseline">
                <p class="text-4xl font-bold text-[#f79039]">{{ $totalVoters }}</p>
                <p class="ml-2 text-sm text-gray-500">pemilih @if($phase != 'all') (Tahap {{ $phase }}) @endif</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Total Pegawai</h3>
            <div class="flex items-baseline">
                <p class="text-4xl font-bold text-gray-900">{{ $totalUsers }}</p>
                <p class="ml-2 text-sm text-gray-500">terdaftar</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Tingkat Partisipasi</h3>
            <div class="flex items-baseline">
                <p class="text-4xl font-bold {{ $participationRate > 80 ? 'text-green-600' : 'text-[#f79039]' }}">
                    {{ number_format($participationRate, 1) }}%
                </p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4">
                <div class="bg-[#f79039] h-2.5 rounded-full" style="width: {{ $participationRate }}%"></div>
            </div>
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-xl">
        <div class="px-8 py-6 border-b border-gray-200 bg-white/50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Peringkat</h2>
            <a href="{{ route('dashboard.export', ['phase' => $phase]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kandidat</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Poin</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sebaran Suara</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-gray-200">
                    @foreach($candidates as $index => $candidate)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-8 py-4 whitespace-nowrap">
                                @if($index < 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $index == 0 ? 'bg-yellow-100 text-yellow-800 ring-2 ring-yellow-300' : '' }}
                                        {{ $index == 1 ? 'bg-gray-100 text-gray-800 ring-2 ring-gray-300' : '' }}
                                        {{ $index == 2 ? 'bg-orange-100 text-orange-800 ring-2 ring-orange-300' : '' }}">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="text-gray-500 ml-2 font-medium">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm" 
                                            src="{{ $candidate->photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode($candidate->name) }}" 
                                            alt="{{ $candidate->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $candidate->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($candidate->description, 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-full inline-block">
                                    {{ $candidate->votes_sum_points ?? 0 }} poin
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex gap-2">
                                    @if($phase == 2)
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#f79039]/20 text-[#f79039] border border-[#f79039]/30" title="Rank 1 (5 poin)">1: {{ $candidate->votes()->where('priority', 1)->where('phase', 2)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#ff8c33]/20 text-[#ff8c33] border border-[#ff8c33]/30" title="Rank 2 (4 poin)">2: {{ $candidate->votes()->where('priority', 2)->where('phase', 2)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#febd26]/20 text-[#febd26] border border-[#febd26]/30" title="Rank 3 (3 poin)">3: {{ $candidate->votes()->where('priority', 3)->where('phase', 2)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#ffd635]/20 text-[#a88f00] border border-[#ffd635]/30" title="Rank 4 (2 poin)">4: {{ $candidate->votes()->where('priority', 4)->where('phase', 2)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#ffe066]/20 text-[#8a7500] border border-[#ffe066]/30" title="Rank 5 (1 poin)">5: {{ $candidate->votes()->where('priority', 5)->where('phase', 2)->count() }}</span>
                                    @elseif($phase == 1)
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#f79039]/20 text-[#f79039] border border-[#f79039]/30" title="Rank 1 (5 poin)">1: {{ $candidate->votes()->where('priority', 1)->where('phase', 1)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#febd26]/20 text-[#febd26] border border-[#febd26]/30" title="Rank 2 (3 poin)">2: {{ $candidate->votes()->where('priority', 2)->where('phase', 1)->count() }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-[#ffd635]/20 text-[#a88f00] border border-[#ffd635]/30" title="Rank 3 (1 poin)">3: {{ $candidate->votes()->where('priority', 3)->where('phase', 1)->count() }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
