@extends('layouts.app')

@section('title','Peminjaman')

@section('content')
<h1 class="text-2xl font-bold mb-4">Transaksi Peminjaman</h1>
<a href="{{ route('loans.create') }}" class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded">Tambah Transaksi</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Anggota</th>
            <th class="px-4 py-2 border">Buku</th>
            <th class="px-4 py-2 border">Tanggal Pinjam</th>
            <th class="px-4 py-2 border">Tanggal Kembali</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
        <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 border">{{ $loan->id }}</td>
            <td class="px-4 py-2 border">{{ $loan->member->name ?? '' }}</td>
            <td class="px-4 py-2 border">{{ $loan->book->title ?? '' }}</td>
            <td class="px-4 py-2 border">{{ $loan->loan_date }}</td>
            <td class="px-4 py-2 border">{{ $loan->return_date }}</td>
            <td class="px-4 py-2 border">{{ $loan->status }}</td>
            <td class="px-4 py-2 border">
                <a href="{{ route('loans.edit',$loan) }}" class="bg-yellow-400 text-white px-2 py-1 rounded">Edit</a>
                <form action="{{ route('loans.destroy',$loan) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $loans->links() }}
@endsection
