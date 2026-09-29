@php
    $category = $category ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="kode_aktiva_tetap" class="form-label">
            {{ __('Kode Aktiva Tetap') }}
        </label>
        <input type="text" id="kode_aktiva_tetap" name="kode_aktiva_tetap" maxlength="2"
            inputmode="numeric" pattern="[0-9]{2}" placeholder="09"
            value="{{ old('kode_aktiva_tetap', $category->kode_aktiva_tetap ?? '') }}"
            {{ $category ? 'readonly' : '' }} required
            class="form-control {{ $category ? 'opacity-60 cursor-not-allowed' : '' }}" />
        @error('kode_aktiva_tetap')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jenis_aktiva_tetap" class="form-label">
            {{ __('Jenis Aktiva Tetap') }}
        </label>
        <input type="text" id="jenis_aktiva_tetap" name="jenis_aktiva_tetap"
            value="{{ old('jenis_aktiva_tetap', $category->jenis_aktiva_tetap ?? '') }}"
            {{ $category ? 'readonly' : '' }} required
            class="form-control {{ $category ? 'opacity-60 cursor-not-allowed' : '' }}" />
        @error('jenis_aktiva_tetap')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sub_jenis" class="form-label">
            {{ __('Sub Jenis') }}
        </label>
        <input type="text" id="sub_jenis" name="sub_jenis" maxlength="2"
            inputmode="numeric" pattern="[0-9]{2}" placeholder="47"
            value="{{ old('sub_jenis', $category->sub_jenis ?? '') }}"
            {{ $category ? 'readonly' : '' }}
            class="form-control {{ $category ? 'opacity-60 cursor-not-allowed' : '' }}" />
        @error('sub_jenis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-sm text-gray-400">
            {{ __('Kosongkan jika jenis ini belum punya sub jenis. Baris tanpa sub jenis tidak muncul di pilihan nomor aktiva.') }}
        </p>
    </div>

    <div>
        <label for="keterangan_fungsi" class="form-label">
            {{ __('Keterangan Fungsi') }}
        </label>
        <input type="text" id="keterangan_fungsi" name="keterangan_fungsi"
            value="{{ old('keterangan_fungsi', $category->keterangan_fungsi ?? '') }}"
            class="form-control" />
        @error('keterangan_fungsi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>
</div>

@if ($category)
    <p class="mt-4 text-sm text-gray-400">
        {{ __('Kode Aktiva Tetap, Jenis, dan Sub Jenis tidak bisa diubah setelah dibuat karena membentuk nomor aktiva.') }}
    </p>
@endif

<div class="mt-6">
    <button type="submit" class="btn btn-primary">
        {{ __('Simpan') }}
    </button>
</div>