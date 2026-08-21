@extends('layouts.app')

@section('content')

    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Manajemen User</h2>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white">Daftar User</h3>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Tambah User
            </a>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $user->email }}</td>
                        <td class="px-6 py-4 capitalize text-gray-800 dark:text-gray-200">{{ $user->role }}</td>
                        <td class="px-6 py-4">
                            @if ($user->is_active)
                                <span class="text-green-600">Aktif</span>
                            @else
                                <span class="text-red-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-600 hover:underline">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>

@endsection