@php
    $item = $item ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="nama_barang" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Nama Barang') }}
        </label>
        <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('nama_barang')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Kategori') }}
        </label>
        <select id="category_id" name="category_id"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">{{ __('-- Pilih Kategori --') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->category_id }}"
                    @selected(old('category_id', $item->category_id ?? '') == $category->category_id)>
                    {{ $category->category_id }} - {{ $category->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Lokasi Pemakaian') }}
        </label>
        <select id="location_id" name="location_id"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">{{ __('-- Pilih Lokasi --') }}</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}"
                    @selected(old('location_id', $item->location_id ?? '') == $location->id)>
                    {{ $location->nama_lokasi }}
                </option>
            @endforeach
        </select>
        @error('location_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

   <div x-data="{
        golongan: '{{ old('golongan_at', $item->golongan_at ?? '') }}',
        isCustom: !['', 'I', 'II'].includes('{{ old('golongan_at', $item->golongan_at ?? '') }}') === false ? false : !['I', 'II', ''].includes('{{ old('golongan_at', $item->golongan_at ?? '') }}')
    }"
    x-init="isCustom = golongan !== '' && golongan !== 'I' && golongan !== 'II'">

    <label for="golongan_at_select" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ __('Golongan AT') }}
    </label>

    <select id="golongan_at_select"
        x-model="golongan"
        @change="isCustom = ($event.target.value === 'lainnya'); if (isCustom) golongan = ''"
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">{{ __('-- Pilih Golongan --') }}</option>
        <option value="I">I</option>
        <option value="II">II</option>
        <option value="lainnya" :selected="isCustom">{{ __('Lainnya (isi manual)') }}</option>
    </select>

    <div x-show="isCustom" class="mt-2">
        <input type="text" x-model="golongan" placeholder="{{ __('Contoh: III, IV, dsb') }}"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
    </div>

    {{-- Field asli yang benar-benar dikirim ke server --}}
    <input type="hidden" name="golongan_at" :value="golongan">

    @error('golongan_at')
        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
    @enderror
</div>

    <div>
        <label for="tahun_perolehan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Tahun Perolehan') }}
        </label>
        <input type="number" id="tahun_perolehan" name="tahun_perolehan" min="1990" max="{{ date('Y') }}"
            value="{{ old('tahun_perolehan', $item->tahun_perolehan ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('tahun_perolehan')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="masa_manfaat" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Masa Manfaat (tahun)') }}
        </label>
        <input type="number" id="masa_manfaat" name="masa_manfaat" min="1" max="50"
            value="{{ old('masa_manfaat', $item->masa_manfaat ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('masa_manfaat')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nilai_perolehan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Nilai Perolehan (Rp)') }}
        </label>
        <input type="number" step="0.01" id="nilai_perolehan" name="nilai_perolehan"
            value="{{ old('nilai_perolehan', $item->nilai_perolehan ?? '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('nilai_perolehan')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kondisi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Kondisi') }}
        </label>
        <select id="kondisi" name="kondisi"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="baik" @selected(old('kondisi', $item->kondisi ?? 'baik') == 'baik')>{{ __('Baik') }}</option>
            <option value="rusak_ringan" @selected(old('kondisi', $item->kondisi ?? '') == 'rusak_ringan')>{{ __('Rusak Ringan') }}</option>
            <option value="rusak_berat" @selected(old('kondisi', $item->kondisi ?? '') == 'rusak_berat')>{{ __('Rusak Berat') }}</option>
        </select>
        @error('kondisi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tanggal_terima" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('Tanggal Terima') }}
        </label>
        <input type="date" id="tanggal_terima" name="tanggal_terima"
            value="{{ old('tanggal_terima', isset($item->tanggal_terima) ? $item->tanggal_terima->format('Y-m-d') : '') }}" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        @error('tanggal_terima')
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