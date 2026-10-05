<x-guest-layout>
    <div class="mb-4 text-theme-sm text-gray-600">
        Lupa password? Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link
        untuk membuat password baru.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 rounded-lg bg-secondary-50 p-3 text-theme-sm font-medium text-secondary-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') !border-error-500 @enderror"
                   required autofocus>
            @error('email')
                <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-primary w-full">
                Kirim Link Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>