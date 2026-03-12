@extends('layouts.app')

@section('title','Tambah Anggota')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Anggota</h1>
<form action="{{ route('members.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">Nama</label>
        <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('name') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">NIM / ID Anggota</label>
        <input type="text" name="member_id" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('member_id') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Alamat</label>
        <input type="text" name="address" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('address') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Telepon</label>
        <input type="text" name="phone" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('phone') }}">
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="mt-1 block w-full border rounded px-3 py-2" value="{{ old('email') }}">
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
</form>
@endsection
