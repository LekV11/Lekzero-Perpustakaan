@extends('layouts.app')

@section('title','Kategori')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar Kategori</h1>
<a href="{{ route('categories.create') }}" class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded">Tambah Kategori</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $cat)
        <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 border">{{ $cat->id }}</td>
            <td class="px-4 py-2 border">{{ $cat->name }}</td>
            <td class="px-4 py-2 border">
                <a href="{{ route('categories.edit',$cat) }}" class="bg-yellow-400 text-white px-2 py-1 rounded">Edit</a>
                <form action="{{ route('categories.destroy',$cat) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $categories->links() }}
@endsection
