@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title">
            Tambah User
        </h2>
        <p class="page-desc">
            Tambahkan akun pengguna baru untuk sistem inventaris.
        </p>
    </div>

    <div class="card card-pad max-w-2xl">

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                @error('name') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                @error('email') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
                @error('password') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div class="mb-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="senior_analis" {{ old('role') == 'senior_analis' ? 'selected' : '' }}>Senior Analis</option>
                    <option value="gm" {{ old('role') == 'gm' ? 'selected' : '' }}>General Manager</option>
                </select>
                @error('role') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </form>

    </div>
@endsection