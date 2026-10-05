<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-control @error('email') !border-error-500 @enderror"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password Baru</label>
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control @error('password') !border-error-500 @enderror"
                   required autocomplete="new-password">
            @error('password')
                <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation"
                   type="password"
                   name="password_confirmation"
                   class="form-control @error('password_confirmation') !border-error-500 @enderror"
                   required autocomplete="new-password">
            @error('password_confirmation')
                <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-primary w-full">
                Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>