@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title">
            Edit User
        </h2>
        <p class="page-desc">
            Perbarui data akun pengguna sistem inventaris.
        </p>
    </div>

    <div class="card card-pad max-w-2xl">

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                @error('name') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                @error('email') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="senior_analis" {{ $user->role == 'senior_analis' ? 'selected' : '' }}>Senior Analis</option>
                    <option value="gm" {{ $user->role == 'gm' ? 'selected' : '' }}>General Manager</option>
                </select>
                @error('role') <p class="text-error-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Update
                </button>
            </div>
        </form>

    </div>
@endsection