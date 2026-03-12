@extends('layouts.app')

@section('title','Anggota')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar Anggota</h1>
<a href="{{ route('members.create') }}" class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded">Tambah Anggota</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">NIM/ID</th>
            <th class="px-4 py-2 border">Telepon</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $member)
        <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 border">{{ $member->id }}</td>
            <td class="px-4 py-2 border">{{ $member->name }}</td>
            <td class="px-4 py-2 border">{{ $member->member_id }}</td>
            <td class="px-4 py-2 border">{{ $member->phone }}</td>
            <td class="px-4 py-2 border">{{ $member->email }}</td>
            <td class="px-4 py-2 border">
                <a href="{{ route('members.edit',$member) }}" class="bg-yellow-400 text-white px-2 py-1 rounded">Edit</a>
                <form action="{{ route('members.destroy',$member) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $members->links() }}
@endsection
