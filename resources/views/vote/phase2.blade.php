@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-3xl p-8 md:p-12">
        <div class="mb-10 text-center">
            <span class="inline-block px-4 py-1 rounded-full bg-orange-100 text-orange-800 text-sm font-bold mb-4">TAHAP 2 - FINAL</span>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pilih Wajah Perubahan BPS Kaltara!</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Suara Anda menentukan siapa yang akan memimpin transformasi. <br>Urutkan finalis favorit Anda dari yang paling 'Berani Berubah dan Berani Berdampak' untuk BPS Kaltara.
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

        <form id="voteForm" action="{{ route('vote.phase2.store') }}" method="POST" class="space-y-6">
            @csrf
            
            @php
                $ranks = [
                    1 => ['bg' => 'bg-orange-50/50', 'border' => 'border-orange-200', 'text' => 'text-[#f79039]', 'points' => 5],
                    2 => ['bg' => 'bg-orange-50/30', 'border' => 'border-orange-100', 'text' => 'text-[#f79039]', 'points' => 4],
                    3 => ['bg' => 'bg-yellow-50/50', 'border' => 'border-yellow-200', 'text' => 'text-[#febd26]', 'points' => 3],
                    4 => ['bg' => 'bg-yellow-50/30', 'border' => 'border-yellow-100', 'text' => 'text-[#febd26]', 'points' => 2],
                    5 => ['bg' => 'bg-gray-50/50', 'border' => 'border-gray-200', 'text' => 'text-gray-600', 'points' => 1],
                ];
            @endphp
            
            @foreach($ranks as $rank => $style)
            <div class="p-4 {{ $style['bg'] }} rounded-xl border {{ $style['border'] }} flex items-center gap-4">
                <div class="w-10 h-10 rounded-full {{ str_replace('text-', 'bg-', $style['text']) }} text-white flex items-center justify-center font-bold text-xl shadow-sm">
                    {{ $rank }}
                </div>
                <div class="flex-grow">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Peringkat {{ $rank }}</label>
                    <select name="candidate_{{ $rank }}" id="candidate_{{ $rank }}" required class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Pilih Finalis...</option>
                        @foreach($candidates as $candidate)
                            <option value="{{ $candidate->id }}" data-name="{{ $candidate->name }}" {{ old('candidate_'.$rank) == $candidate->id ? 'selected' : '' }}>
                                {{ $candidate->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endforeach

            <div class="pt-6">
                <button type="button" onclick="confirmVote()" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-full shadow-lg text-lg font-bold text-white bg-gradient-to-r from-[#f79039] to-[#febd26] hover:from-[#e08030] hover:to-[#e5aa20] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f79039] transform transition-all duration-150 hover:scale-[1.02]">
                    Kirim Peringkat Final
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmVote() {
    // Get selected candidates
    const ranks = [1, 2, 3, 4, 5];
    let listHtml = '';
    let allSelected = true;
    let selectedValues = [];
    let candidateNames = {};

    for (const rank of ranks) {
        const select = document.getElementById('candidate_' + rank);
        const selectedOption = select.options[select.selectedIndex];
        
        if (!select.value) {
            allSelected = false;
            break;
        }
        
        selectedValues.push(select.value);
        const candidateName = selectedOption.getAttribute('data-name') || selectedOption.text;
        candidateNames[select.value] = candidateName;
        
        listHtml += `<div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
            <span class="w-8 h-8 rounded-full bg-gradient-to-r from-[#f79039] to-[#febd26] text-white flex items-center justify-center font-bold text-sm">${rank}</span>
            <span class="font-medium text-gray-800">${candidateName}</span>
        </div>`;
    }

    if (!allSelected) {
        Swal.fire({
            icon: 'warning',
            title: 'Belum Lengkap',
            text: 'Harap pilih kandidat untuk semua peringkat!',
            confirmButtonColor: '#f79039'
        });
        return;
    }

    // Check for duplicate selections
    const duplicates = selectedValues.filter((item, index) => selectedValues.indexOf(item) !== index);
    if (duplicates.length > 0) {
        const duplicateNames = [...new Set(duplicates)].map(id => candidateNames[id]).join(', ');
        Swal.fire({
            icon: 'error',
            title: 'Ada Pilihan Ganda',
            html: `<p>Kandidat <strong>${duplicateNames}</strong> dipilih lebih dari satu kali.</p><p class="mt-2 text-gray-600">Setiap kandidat hanya boleh dipilih satu kali.</p>`,
            confirmButtonColor: '#f79039'
        });
        return;
    }

    // First confirmation - show the list
    Swal.fire({
        title: 'Konfirmasi Pilihan Anda',
        html: `<div class="text-left mt-4">${listHtml}</div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sudah Benar',
        cancelButtonText: 'Ubah Pilihan',
        confirmButtonColor: '#f79039',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Second confirmation - final confirmation
            Swal.fire({
                title: 'Yakin dengan Pilihan Anda?',
                html: '<p class="text-gray-600">Pilihan yang sudah dikirim <strong>tidak dapat diubah</strong>.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f79039',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Mengirim...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('voteForm').submit();
                }
            });
        }
    });
}
</script>
@endsection
