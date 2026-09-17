@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Manajemen User
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola data pengguna sistem inventaris.
            </p>
        </div>

         <a href="{{ route('admin.users.create') }}" class="bg-brand-500 text-white px-4 py-2 rounded hover:bg-brand-600">
            + Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Nama
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Email
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Role
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $users->firstItem() + $loop->index }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $user->name }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $user->email }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $user->role === 'admin'
                                    ? 'bg-purple-100 text-purple-800'
                                    : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $user->is_active
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    title="Edit"
                                    class="p-2 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
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
                                        class="p-2 rounded text-white transition
                                        {{ $user->is_active
                                            ? 'bg-red-500 hover:bg-red-600'
                                            : 'bg-green-500 hover:bg-green-600' }}">
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
                        <td colspan="6"
                            class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data user.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection