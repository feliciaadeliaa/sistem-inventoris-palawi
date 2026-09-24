@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title">
            {{ __('Profile') }}
        </h2>
        <p class="page-desc">
            {{ __('Kelola informasi akun dan keamanan kamu.') }}
        </p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ __('Profil berhasil diperbarui.') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">

        {{-- Avatar + Info Dasar --}}
        <div class="card card-pad">
            <h3 class="panel-title">
                {{ __('Informasi Profil') }}
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('patch')

                {{-- Avatar --}}
                <div class="flex items-center gap-5">
                    <img
                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                        alt="{{ $user->name }}"
                        class="h-20 w-20 rounded-full object-cover border border-gray-200"
                    />
                    <div class="flex-1">
                        <label for="avatar" class="form-label">
                            {{ __('Foto Profil') }}
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100" />
                        @error('avatar')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="form-label">
                        {{ __('Nama') }}
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="form-control" />
                    @error('name')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="form-label">
                        {{ __('Email') }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="form-control" />
                    @error('email')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Email kamu belum diverifikasi.') }}
                            <button form="send-verification" class="text-brand-600 underline hover:text-brand-700">
                                {{ __('Klik untuk kirim ulang email verifikasi.') }}
                            </button>
                        </p>
                    @endif
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
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

        {{-- Ganti Password --}}
        <div class="card card-pad">
            <h3 class="panel-title">
                {{ __('Ubah Password') }}
            </h3>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="form-label">
                        {{ __('Password Saat Ini') }}
                    </label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="form-control" />
                    @error('current_password', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">
                        {{ __('Password Baru') }}
                    </label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="form-control" />
                    @error('password', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">
                        {{ __('Konfirmasi Password Baru') }}
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="form-control" />
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Simpan Password') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Hapus Akun --}}
        <div class="card card-pad border border-error-200">
            <h3 class="panel-title mb-2">
                {{ __('Hapus Akun') }}
            </h3>
            <p class="mb-5 text-sm text-gray-500">
                {{ __('Setelah akun dihapus, semua data akan hilang secara permanen. Pastikan kamu sudah yakin sebelum melanjutkan.') }}
            </p>

            <button type="button" onclick="document.getElementById('delete-account-form').classList.toggle('hidden')"
                class="btn border border-error-500 bg-white text-error-500 hover:bg-error-50">
                {{ __('Hapus Akun Saya') }}
            </button>

            <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="mt-5 hidden space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" class="form-label">
                        {{ __('Masukkan password untuk konfirmasi') }}
                    </label>
                    <input type="password" id="delete_password" name="password"
                        class="form-control max-w-sm" />
                    @error('password', 'userDeletion')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn bg-error-500 text-white hover:bg-error-600">
                    {{ __('Konfirmasi Hapus Akun') }}
                </button>
            </form>
        </div>

    </div>
@endsection