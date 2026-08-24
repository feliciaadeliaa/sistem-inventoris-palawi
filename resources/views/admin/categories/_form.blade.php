{{-- resources/views/admin/categories/_form.blade.php --}}
@php
    $category = $category ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Kode Kategori') }}
        </label>
        <input type="text" id="category_id" name="category_id" maxlength="5"
            value="{{ old('category_id', $category->category_id ?? '') }}"
            {{ $category ? 'readonly' : '' }} placeholder="C06" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 {{ $category ? 'opacity-60 cursor-not-allowed' : '' }}" />
        @error('category_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
        @if ($category)
            <p class="mt-1.5 text-sm text-gray-400">{{ __('Kode kategori tidak bisa diubah setelah dibuat.') }}</p>
        @endif
    </div>

    <div>
        <label for="nama_kategori" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Nama Kategori') }}
        </label>
        <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $category->nama_kategori ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('nama_kategori')
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