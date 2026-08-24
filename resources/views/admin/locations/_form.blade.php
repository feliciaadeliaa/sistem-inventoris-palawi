{{-- resources/views/admin/locations/_form.blade.php --}}
@php
    $location = $location ?? null;
@endphp

<div>
    <label for="nama_lokasi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ __('Nama Lokasi') }}
    </label>
    <input type="text" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $location->nama_lokasi ?? '') }}" required
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full max-w-md rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
    @error('nama_lokasi')
        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
    @enderror
</div>

<div class="mt-6">
    <button type="submit"
        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
        {{ __('Simpan') }}
    </button>
</div>