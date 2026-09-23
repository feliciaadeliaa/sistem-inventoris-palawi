@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">
                Manajemen User
            </h2>
            <p class="page-desc">
                Kelola data pengguna sistem inventaris.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="text-gray-500">
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $user->name }}
                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-purple' : 'badge-blue' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        title="Edit"
                                        class="icon-btn icon-btn-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.users.toggle-active', $user) }}"
                                        method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="icon-btn {{ $user->is_active ? 'icon-btn-danger' : 'icon-btn-success' }}">
                                            @if ($user->is_active)
                                                {{-- icon: user-x (nonaktifkan) --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <line x1="17" y1="8" x2="22" y2="13" />
                                                    <line x1="22" y1="8" x2="17" y2="13" />
                                                </svg>
                                            @else
                                                {{-- icon: user-check (aktifkan) --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <polyline points="16 11 18 13 22 9" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-pad !pt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection