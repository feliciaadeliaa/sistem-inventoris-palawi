@php
    $golongan = $golongan ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="kode" class="form-label">{{ __('Kode') }}</label>
        <input type="text" id="kode" name="kode" maxlength="2" placeholder="01"
            value="{{ old('kode', $golongan->kode ?? '') }}" required class="form-control" />
        @error('kode')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nama" class="form-label">{{ __('Nama Golongan') }}</label>
        <input type="text" id="nama" name="nama" placeholder="Golongan I"
            value="{{ old('nama', $golongan->nama ?? '') }}" required class="form-control" />
        @error('nama')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="masa_manfaat" class="form-label">{{ __('Masa Manfaat (tahun)') }}</label>
        <input type="number" id="masa_manfaat" name="masa_manfaat" min="1" max="50"
            value="{{ old('masa_manfaat', $golongan->masa_manfaat ?? '') }}" required class="form-control" />
        @error('masa_manfaat')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="persen" class="form-label">{{ __('Persen (% per tahun)') }}</label>
        <input type="number" step="0.01" id="persen" name="persen" min="0" max="100"
            value="{{ old('persen', $golongan->persen ?? '') }}" required class="form-control" />
        @error('persen')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
    <a href="{{ route('golongan.index') }}" class="btn btn-outline">{{ __('Batal') }}</a>
</div>