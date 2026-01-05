<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan CC 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(247, 144, 57, 0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#f79039] to-[#febd26] min-h-screen flex flex-col">
    <nav class="bg-white/90 backdrop-blur-md border-b border-orange-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#f79039] to-[#ffd635]">
                        Pemilihan CC 2026
                    </span>
                </div>

                @auth
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->username === 'admin')
                        <div class="hidden md:flex space-x-4 mr-4 border-r border-gray-200 pr-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-[#f79039] px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                            <a href="{{ route('candidates.index') }}" class="text-gray-600 hover:text-[#f79039] px-3 py-2 rounded-md text-sm font-medium transition-colors">Kandidat</a>
                            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-[#f79039] px-3 py-2 rounded-md text-sm font-medium transition-colors">Pengguna</a>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">
                            Halo, {{ auth()->user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                             @csrf
                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div id="flash-message" class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex justify-between items-center animate-fade-in-down">
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
                <button onclick="document.getElementById('flash-message').style.display='none'" class="text-green-500 hover:text-green-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
        
        @if(session('error'))
             <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
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

        @yield('content')
    </main>

    <footer class="bg-white/80 py-4 mt-auto">
        <div class="container mx-auto text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Tim CC/CA Tahun 2025
        </div>
    </footer>
</body>
</html>
