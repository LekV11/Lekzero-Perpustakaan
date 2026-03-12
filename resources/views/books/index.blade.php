@extends('layouts.app')

@section('title','Buku')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Daftar Buku</h1>
    <div class="flex items-center space-x-2 mt-4 md:mt-0 w-full md:w-auto">
        <form action="{{ route('books.index') }}" method="GET" class="flex w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau penulis..." class="border rounded-l px-4 py-2 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r hover:bg-blue-600">Cari</button>
        </form>
        @if(request('search'))
            <a href="{{ route('books.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline whitespace-nowrap">Reset</a>
        @endif
    </div>
</div>

@auth
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('books.create') }}" class="inline-block mb-6 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah Buku Baru</a>
    @endif
@endauth

@if(auth()->check() && auth()->user()->role === 'admin')
    <!-- admin table view -->
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($books as $book)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $book->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($book->cover_path)
                            <img src="{{ asset('storage/'.$book->cover_path) }}" class="h-12 w-8 object-cover rounded shadow-sm" alt="cover">
                        @else
                            <div class="h-12 w-8 bg-gray-200 rounded flex items-center justify-center text-[8px] text-gray-400">NO COVER</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $book->title }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $book->author }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $book->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $book->stock }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('books.edit',$book) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 px-3 py-1 rounded">Edit</a>
                            <form action="{{ route('books.destroy',$book) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-900 bg-red-100 px-3 py-1 rounded" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">Buku tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $books->appends(['search' => request('search')])->links() }}
    </div>
@else
    <!-- card grid for regular users (Detail link removed) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($books as $book)
        <div class="bg-white shadow-md hover:shadow-lg transition-shadow rounded-lg overflow-hidden flex flex-col">
            <div class="h-48 bg-gray-100 relative">
                @if($book->cover_path)
                    <img src="{{ asset('storage/'.$book->cover_path) }}" class="h-full w-full object-cover" alt="cover">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 italic">Tanpa Cover</div>
                @endif
                <div class="absolute top-2 right-2">
                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full shadow-md">{{ $book->category->name ?? 'Umum' }}</span>
                </div>
            </div>
            <div class="p-4 flex-grow">
                <h2 class="text-xl font-bold mb-2 text-gray-800 line-clamp-2" title="{{ $book->title }}">{{ $book->title }}</h2>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-semibold">Penulis:</span> {{ $book->author }}</p>
                    <p><span class="font-semibold">Stok:</span> 
                        <span class="{{ $book->stock > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                            {{ $book->stock }} tersedia
                        </span>
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <p class="text-gray-500 text-lg">Maaf, buku yang Anda cari tidak tersedia.</p>
        </div>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $books->appends(['search' => request('search')])->links() }}
    </div>
@endif
@endsection
