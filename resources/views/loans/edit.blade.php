@extends('layouts.app')

@section('title','Edit Peminjaman')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Transaksi</h1>
<form action="{{ route('loans.update',$loan) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium">Anggota</label>
        <select name="member_id" class="mt-1 block w-full border rounded px-3 py-2">
            @foreach($members as $m)
                <option value="{{ $m->id }}" {{ $loan->member_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Buku</label>
        <select name="book_id" class="mt-1 block w-full border rounded px-3 py-2">
            @foreach($books as $b)
                <option value="{{ $b->id }}" {{ $loan->book_id == $b->id ? 'selected' : '' }}>{{ $b->title }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Tanggal Pinjam</label>
        <input type="date" name="loan_date" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('loan_date',$loan->loan_date) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Tanggal Kembali</label>
        <input type="date" name="return_date" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('return_date',$loan->return_date) }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="mt-1 block w-full border rounded px-3 py-2">
            <option value="borrowed" {{ $loan->status == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
            <option value="returned" {{ $loan->status == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
        </select>
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
