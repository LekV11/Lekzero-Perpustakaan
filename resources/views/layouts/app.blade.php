<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan')</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<nav class="bg-white shadow mb-4">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="text-xl font-semibold text-gray-800">Perpustakaan</a>
            </div>
            @auth
            <div class="flex space-x-4">
                <a href="{{ route('books.index') }}" class="text-gray-600 hover:text-gray-800">Buku</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('members.index') }}" class="text-gray-600 hover:text-gray-800">Anggota</a>
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-800">Kategori</a>
                    <a href="{{ route('loans.index') }}" class="text-gray-600 hover:text-gray-800">Peminjaman</a>
                @endif
            </div>
            @endauth
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Register</a>
                @endguest
            </div>
        </div>
    </div>
</nav>
<div class="max-w-7xl mx-auto px-4">
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>
