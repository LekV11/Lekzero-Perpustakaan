@extends('layouts.app')

@section('title','Edit Buku')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Buku</h1>
<form action="{{ route('books.update',$book) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium">Judul</label>
        <input type="text" name="title" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('title',$book->title) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Penulis</label>
        <input type="text" name="author" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('author',$book->author) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Penerbit</label>
        <input type="text" name="publisher" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('publisher',$book->publisher) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Tahun</label>
        <input type="number" name="year" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('year',$book->year) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Kategori</label>
        <select name="category_id" class="mt-1 block w-full border rounded px-3 py-2">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Stok</label>
        <input type="number" name="stock" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('stock',$book->stock) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Cover saat ini</label>
        @if($book->cover_path)
            <img src="{{ asset('storage/'.$book->cover_path) }}" class="h-32 mb-2" alt="cover">
        @else
            <p class="text-gray-500">Tidak ada gambar</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium">Ganti cover</label>
        <input type="file" name="cover" accept="image/*" class="mt-1 block w-full">
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
