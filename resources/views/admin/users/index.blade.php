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

        <a href="{{ route('admin.users.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Edit
                                </a>

                                <form action="{{ route('admin.users.toggle-active', $user) }}"
                                    method="POST"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="px-3 py-1 rounded text-white
                                        {{ $user->is_active
                                            ? 'bg-red-500 hover:bg-red-600'
                                            : 'bg-green-500 hover:bg-green-600' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
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