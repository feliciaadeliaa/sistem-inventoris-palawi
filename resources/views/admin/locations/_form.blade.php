@php
    $location = $location ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl">
    <div>
        <label for="nama_lokasi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Nama Lokasi') }}
        </label>
        <input type="text" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $location->nama_lokasi ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('nama_lokasi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kode_lokasi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Kode Lokasi') }}
        </label>
        <input type="text" id="kode_lokasi" name="kode_lokasi" value="{{ old('kode_lokasi', $location->kode_lokasi ?? '') }}"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('kode_lokasi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="wilayah" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Wilayah') }}
        </label>
        <input type="text" id="wilayah" name="wilayah" value="{{ old('wilayah', $location->wilayah ?? '') }}"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('wilayah')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="unit_bisnis" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Unit Bisnis') }}
        </label>
        <input type="text" id="unit_bisnis" name="unit_bisnis" value="{{ old('unit_bisnis', $location->unit_bisnis ?? '') }}"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('unit_bisnis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sub_unit_bisnis" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Sub Unit Bisnis') }}
        </label>
        <input type="text" id="sub_unit_bisnis" name="sub_unit_bisnis" value="{{ old('sub_unit_bisnis', $location->sub_unit_bisnis ?? '') }}"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('sub_unit_bisnis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <button type="submit"
        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
        {{ __('Simpan') }}
    </button>
</div>