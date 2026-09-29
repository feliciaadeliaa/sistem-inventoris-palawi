@php
    $location = $location ?? null;
    $isEdit = $location && $location->exists;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl">
    {{-- Kode klaster: 1 digit --}}
    <div>
        <label for="kode_unit_bisnis" class="form-label">
            {{ __('Kode Klaster') }}
        </label>
        @if ($isEdit)
            <input type="text" id="kode_unit_bisnis" value="{{ $location->kode_unit_bisnis }}" readonly
                class="form-control bg-gray-50" />
        @else
            <input type="text" id="kode_unit_bisnis" name="kode_unit_bisnis"
                value="{{ old('kode_unit_bisnis') }}" required
                maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="1"
                class="form-control" />
            <p class="mt-1.5 text-sm text-gray-400">{{ __('1 digit angka, contoh: 1') }}</p>
        @endif
        @error('kode_unit_bisnis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama klaster --}}
    <div>
        <label for="unit_bisnis" class="form-label">
            {{ __('Nama Klaster') }}
        </label>
        @if ($isEdit)
            <input type="text" id="unit_bisnis" value="{{ $location->unit_bisnis }}" readonly
                class="form-control bg-gray-50" />
        @else
            <input type="text" id="unit_bisnis" name="unit_bisnis"
                value="{{ old('unit_bisnis') }}" required
                placeholder="KLASTER MALANG"
                class="form-control" />
            <p class="mt-1.5 text-sm text-gray-400">{{ __('Satu kode klaster harus punya nama yang sama.') }}</p>
        @endif
        @error('unit_bisnis')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kode lokasi: 2 digit --}}
    <div>
        <label for="kode_lokasi" class="form-label">
            {{ __('Kode Lokasi') }}
        </label>
        @if ($isEdit)
            <input type="text" id="kode_lokasi" value="{{ $location->kode_lokasi }}" readonly
                class="form-control bg-gray-50" />
        @else
            <input type="text" id="kode_lokasi" name="kode_lokasi"
                value="{{ old('kode_lokasi') }}" required
                maxlength="2" inputmode="numeric" pattern="[0-9]{2}" placeholder="01"
                class="form-control" />
            <p class="mt-1.5 text-sm text-gray-400">{{ __('2 digit angka dan unik, contoh: 01') }}</p>
        @endif
        @error('kode_lokasi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama wisata --}}
    <div>
        <label for="nama_wisata" class="form-label">
            {{ __('Nama Wisata') }}
        </label>
        <input type="text" id="nama_wisata" name="nama_wisata"
            value="{{ old('nama_wisata', $location->nama_wisata ?? '') }}" required
            class="form-control" />
        @error('nama_wisata')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    @if ($isEdit)
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">
                {{ __('Kode klaster dan kode lokasi membentuk nomor aktiva tetap, jadi tidak bisa diubah. Kode gabungan:') }}
                <span class="font-medium text-gray-700">{{ $location->kode_gabungan }}</span>
            </p>
        </div>
    @endif
</div>

<div class="mt-6">
    <button type="submit" class="btn btn-primary">
        {{ __('Simpan') }}
    </button>
</div>