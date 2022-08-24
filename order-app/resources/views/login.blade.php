@extends('app')

@section('content')
    <div class="flex justify-center">
        <div class="w-6/12 bg-white p-6 rounded-lg">
            <div class="text-red-500 mb-2 text-sm">
                @if (session('status'))
                    {{ session('status') }}
                @endif
            </div>

            <form action="{{ route('admin_login') }}" method="POST">
                @csrf


                <div class="mb-4">
                    <label for="username" class="sr-only">Username</label>
                    <input type="text" name="username" id="username" placeholder="Your username"
                        class="bg-gray-100 border-2 w-full p-4 rounded-lg" @error('username') border-red-500 @enderror
                        value="{{ old('username') }}">
                    @error('username')
                        <div class="text-red-500 mt-2 text-sm">
                            {{ $message }}
                        </div>

                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="sr-only">password</label>
                    <input type="password" name="password" id="password" placeholder="Enter a password"
                        class="bg-gray-100 border-2 w-full p-4 rounded-lg" @error('password') border-red-500 @enderror
                        value="">
                    @error('password')
                        <div class="text-red-500 mt-2 text-sm">
                            {{ $message }}
                        </div>

                    @enderror
                </div>
                <div class="mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="mr-2">
                        <label for="remember">Remember me</label>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-3
                                                                                                                            rounded font-medium w-full">Login</button>
                    </div>
            </form>
        </div>
    </div>
@endsection
