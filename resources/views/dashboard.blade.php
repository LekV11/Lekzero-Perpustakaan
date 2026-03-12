@extends('layouts.app')

@section('title','Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>
<p class="mb-4 text-gray-700">Anda masuk sebagai <strong>{{ auth()->user()->role }}</strong>.</p>

@if(auth()->user()->role === 'admin')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white shadow rounded p-4">
        <h5 class="text-lg font-medium">Jumlah buku</h5>
        <p class="text-2xl">{{ $books_count }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <h5 class="text-lg font-medium">Jumlah anggota</h5>
        <p class="text-2xl">{{ $members_count }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <h5 class="text-lg font-medium">Buku dipinjam</h5>
        <p class="text-2xl">{{ $borrowed_count }}</p>
    </div>
    <div class="bg-white shadow rounded p-4">
        <h5 class="text-lg font-medium">Buku tersedia</h5>
        <p class="text-2xl">{{ $available_count }}</p>
    </div>
</div>
@else
<div class="bg-white shadow rounded p-4">
    <h5 class="text-lg font-medium">Jumlah peminjaman saya</h5>
    <p class="text-2xl">{{ $my_loans }}</p>
</div>
@endif
@endsection
