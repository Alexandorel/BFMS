<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} - Register </title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen flex items center justify-center bg-gray-50">
        <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold text-center mb-6">Register</h1>

            <form method="POST" action="{{ route('register') }}">
                @csrf 

                {{-- First Name --}}
                <div class="mb-4">
                    <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input id="first-name" type="text" 
                    name="first-name" value="{{ old('first-name') }}" 
                    required autofocus
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring2 focus:ring-blue-500">
                    
                    @error('name')
                    <p class="text-sm text-red-600 mt-1"> {{ $message }} </p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="mb-4">
                    <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input id="last-name" type="text" 
                    name="last-name" value="{{ old('last-name') }}" 
                    required autofocus
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring2 focus:ring-blue-500">
                    
                    @error('last-name')
                    <p class="text-sm text-red-600 mt-1"> {{ $message }} </p>
                    @enderror
                </div>

                {{--Email--}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" 
                    name="email" value="{{ old('email') }}" 
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring2 focus:ring-blue-500">

                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{--Password--}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" 
                    name="password"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring2 focus:ring-blue-500">

                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{--Confirm-Password--}}
                <div class="mb-6">
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input id="password_confirm" type="password" 
                    name="password_confirm"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring2 focus:ring-blue-500">
                </div>

                <button type="submit" 
                class="w-full bg-blue-600 text-white font-medium py-2 rounded-md hover:bg-blue-700 transition">
                Register
                </button>
            </form>

            <p class="text-sm text-center text-gray-500 mt-6">
                Already have an account?
                <a {{-- href="{{ route('login') }}"  --}} class="text-blue-600 hover:underline"> Sign In </a>
            </p>
        </div>
    </body>
</html>

