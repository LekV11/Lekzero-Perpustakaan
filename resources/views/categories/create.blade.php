@extends('layouts.app')

@section('title','Tambah Kategori')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Kategori</h1>
<form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">Nama</label>
        <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('name') }}">
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
