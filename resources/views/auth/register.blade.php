@extends('layouts.app')

@section('title','Register')

@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar</h1>
<form action="{{ route('register.post') }}" method="POST" class="space-y-4 max-w-md">
    @csrf
    <div>
        <label class="block text-sm font-medium">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full border rounded px-3 py-2">
        @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full border rounded px-3 py-2">
        @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" required class="mt-1 block w-full border rounded px-3 py-2">
        @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required class="mt-1 block w-full border rounded px-3 py-2">
    </div>
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Register</button>
</form>
@endsection
