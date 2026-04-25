@extends('layouts.app')

@section('title','Login')

@section('content')
<h1 class="text-2xl font-bold mb-4">Masuk</h1>
<form action="{{ route('login.post') }}" method="POST" class="space-y-4 max-w-md">
    @csrf
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
    <div class="flex items-center space-x-4">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Login</button>
        <a href="{{ route('google.login') }}" class="bg-red-500 text-white px-4 py-2 rounded">Login with Google</a>
    </div>
</form>
@endsection
