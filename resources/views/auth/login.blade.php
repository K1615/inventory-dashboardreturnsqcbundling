@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900">Sign in</h2>
        <p class="mt-2 text-sm text-slate-500">Use your ERP account to continue.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                class="w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition focus:border-navyBlue focus:ring-2 focus:ring-blue-100 {{ $errors->has('email') ? 'border-red-400' : 'border-slate-300' }}"
            >
            @error('email')
                <p id="email-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                class="w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition focus:border-navyBlue focus:ring-2 focus:ring-blue-100 {{ $errors->has('password') ? 'border-red-400' : 'border-slate-300' }}"
            >
            @error('password')
                <p id="password-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <label for="remember" class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                value="1"
                @checked(old('remember'))
                class="h-4 w-4 rounded border-slate-300 text-navyBlue focus:ring-navyBlue"
            >
            Keep me signed in
        </label>

        <button
            type="submit"
            class="w-full rounded-lg bg-navyBlue px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-navyBlue focus:ring-offset-2"
        >
            Sign in
        </button>
    </form>
</div>
@endsection
