@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Tambah User
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Tambahkan akun pengguna baru untuk sistem inventaris.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm max-w-2xl">

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" name="password"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                <select name="role"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="senior_analis" {{ old('role') == 'senior_analis' ? 'selected' : '' }}>Senior Analis</option>
                    <option value="gm" {{ old('role') == 'gm' ? 'selected' : '' }}>General Manager</option>
                </select>
                @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-brand-500 text-white hover:bg-brand-600 transition">
                    Simpan
                </button>
            </div>
        </form>

    </div>
@endsection