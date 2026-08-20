@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Profile') }}
        </h2>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            {{ __('Profil berhasil diperbarui.') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">

        <!-- Avatar + Info Dasar -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90">
                {{ __('Informasi Profil') }}
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('patch')

                <!-- Avatar -->
                <div class="flex items-center gap-5">
                    <img
                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                        alt="{{ $user->name }}"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                    <div class="flex-1">
                        <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            {{ __('Foto Profil') }}
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-white/5 dark:file:text-brand-400" />
                        @error('avatar')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Nama') }}
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('name')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Email') }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('email')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Email kamu belum diverifikasi.') }}
                            <button form="send-verification" class="text-brand-500 underline hover:text-brand-600">
                                {{ __('Klik untuk kirim ulang email verifikasi.') }}
                            </button>
                        </p>
                    @endif
                </div>

                <div>
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                        {{ __('Simpan') }}
                    </button>
                </div>
            </form>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            @endif
        </div>

        <!-- Ganti Password -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90">
                {{ __('Ubah Password') }}
            </h3>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Password Saat Ini') }}
                    </label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('current_password', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Password Baru') }}
                    </label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('password', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Konfirmasi Password Baru') }}
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                        {{ __('Simpan Password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Hapus Akun -->
        <div class="rounded-2xl border border-error-200 bg-white p-6 dark:border-error-800 dark:bg-white/[0.03]">
            <h3 class="mb-2 text-lg font-medium text-gray-800 dark:text-white/90">
                {{ __('Hapus Akun') }}
            </h3>
            <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Setelah akun dihapus, semua data akan hilang secara permanen. Pastikan kamu sudah yakin sebelum melanjutkan.') }}
            </p>

            <button type="button" onclick="document.getElementById('delete-account-form').classList.toggle('hidden')"
                class="rounded-lg border border-error-500 px-5 py-2.5 text-sm font-medium text-error-500 transition hover:bg-error-50 dark:hover:bg-error-500/10">
                {{ __('Hapus Akun Saya') }}
            </button>

            <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="mt-5 hidden space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Masukkan password untuk konfirmasi') }}
                    </label>
                    <input type="password" id="delete_password" name="password"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 h-11 w-full max-w-sm rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('password', 'userDeletion')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="rounded-lg bg-error-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-error-600">
                    {{ __('Konfirmasi Hapus Akun') }}
                </button>
            </form>
        </div>

    </div>
@endsection