@php
    $category = $category ?? null;
    $golongans = $golongans ?? \App\Models\Golongan::orderBy('kode')->get();

    // Edit: Sub Jenis hanya bisa diubah kalau kategori belum dipakai aset
    $subJenisTerkunci = $category && !$category->subJenisBolehDiubah();
    $golonganTerkunci = $category && !$category->golonganBolehDiubah();
    $selectedGolongan = old('golongan_id', $category->golongan_id ?? '');
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
            {{ $subJenisTerkunci ? 'readonly' : '' }}
            class="form-control {{ $subJenisTerkunci ? 'opacity-60 cursor-not-allowed' : '' }}" />
        @error('sub_jenis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-sm text-gray-400">
            @if ($subJenisTerkunci)
                {{ __('Sub Jenis dikunci karena kategori ini sudah dipakai aset. Mengubahnya membuat nomor aktiva aset yang sudah ada tidak cocok lagi dengan kategorinya.') }}
            @else
                {{ __('Kosongkan jika jenis ini belum punya sub jenis. Baris tanpa sub jenis tidak muncul di pilihan nomor aktiva.') }}
            @endif
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

    {{-- Golongan AT: menentukan masa manfaat aset di kategori ini --}}
    <div class="md:col-span-2">
        <label for="golongan_id" class="form-label">
            {{ __('Golongan AT') }}
        </label>
        <select id="golongan_id" name="golongan_id"
            class="form-control {{ $golonganTerkunci ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}"
            @disabled($golonganTerkunci)>
            <option value="">{{ __('-- Pilih Golongan AT --') }}</option>
            @foreach ($golongans as $golongan)
                <option value="{{ $golongan->id }}" @selected((string) $selectedGolongan === (string) $golongan->id)>
                    {{ $golongan->nama }} ({{ $golongan->masa_manfaat }} {{ __('tahun') }})
                </option>
            @endforeach
        </select>
        @error('golongan_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror

        @if ($golonganTerkunci)
            <p class="mt-1.5 text-sm text-gray-400">
                {{ __('Golongan dikunci karena kategori ini sudah dipakai aset. Mengubahnya akan mengubah masa manfaat dan penyusutan semua aset di kategori ini.') }}
            </p>
        @else
            <p class="mt-1.5 text-sm text-gray-400">
                {{ __('Wajib diisi jika Sub Jenis diisi. Golongan menentukan masa manfaat aset.') }}
            </p>
        @endif
    </div>
</div>

@if ($category)
    <p class="mt-4 text-sm text-gray-400">
        {{ __('Kode Aktiva Tetap dan Jenis tidak bisa diubah setelah dibuat karena membentuk nomor aktiva.') }}
    </p>
@endif

<div class="mt-6">
    <button type="submit" class="btn btn-primary">
        {{ __('Simpan') }}
    </button>
</div>

{{-- Golongan jadi wajib begitu Sub Jenis diisi (halaman tambah, atau edit kategori yang Sub Jenisnya boleh diubah) --}}
@if (!$subJenisTerkunci && !$golonganTerkunci)
    <script>
        (function () {
            const sub = document.getElementById('sub_jenis');
            const gol = document.getElementById('golongan_id');
            function sync() { gol.required = sub.value.trim() !== ''; }
            sub.addEventListener('input', sync);
            sync();
        })();
    </script>
@endif