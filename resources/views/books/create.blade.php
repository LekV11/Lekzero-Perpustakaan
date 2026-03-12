@extends('layouts.app')

@section('title','Tambah Buku')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Buku</h1>
<form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">Judul</label>
        <input type="text" name="title" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('title') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Penulis</label>
        <input type="text" name="author" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('author') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Penerbit</label>
        <input type="text" name="publisher" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('publisher') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Tahun</label>
        <input type="number" name="year" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('year') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Kategori</label>
        <select name="category_id" class="mt-1 block w-full border rounded px-3 py-2">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Stok</label>
        <input type="number" name="stock" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('stock',0) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Cover (gambar)</label>
        <input type="file" name="cover" accept="image/*" class="mt-1 block w-full">
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
