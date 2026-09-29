@php
    $item = $item ?? null;

    $selectedCategoryId = old('category_id', $item->category_id ?? null);
    $selectedLocationId = old('location_id', $item->location_id ?? null);

    // Kelompokkan per Kode Aktiva Tetap untuk dropdown kiri dan kanan
    $groups = $categories->groupBy('kode_aktiva_tetap')->map(fn ($rows, $kode) => [
        'kode'  => (string) $kode,
        'jenis' => $rows->first()->jenis_aktiva_tetap,
        'subs'  => $rows->map(fn ($c) => [
            'id'         => $c->category_id,
            'sub_jenis'  => $c->sub_jenis,
            'keterangan' => $c->keterangan_fungsi,
        ])->values(),
    ])->values();

    $selectedKode = $selectedCategoryId
        ? optional($categories->firstWhere('category_id', $selectedCategoryId))->kode_aktiva_tetap
        : null;

    // Kelompokkan lokasi per klaster (kode_unit_bisnis)
    $klasters = $locations->groupBy('kode_unit_bisnis')->map(fn ($rows, $kode) => [
        'kode'   => (string) $kode,
        'nama'   => $rows->first()->unit_bisnis,
        'wisata' => $rows->map(fn ($l) => [
            'id'   => $l->id,
            'kode' => $l->kode_lokasi,
            'nama' => $l->nama_wisata,
        ])->values(),
    ])->values();

    $selectedKlaster = $selectedLocationId
        ? optional($locations->firstWhere('id', $selectedLocationId))->kode_unit_bisnis
        : null;

    $selectedGolongan = old('golongan_at', $item->golongan_at ?? '');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="nama_barang" class="form-label">
            {{ __('Nama Barang') }}
        </label>
        <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? '') }}" required
            class="form-control" />
        @error('nama_barang')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Golongan AT = jenis aset: I = AT (digit 1), II = IBAT (digit 2) --}}
    <div>
        <label for="golongan_at" class="form-label">
            {{ __('Golongan AT') }}
        </label>
        <select id="golongan_at" name="golongan_at" class="form-control" required>
            <option value="">{{ __('-- Pilih Golongan --') }}</option>
            @foreach (\App\Models\Item::GOLONGAN_LABELS as $kode => $label)
                <option value="{{ $kode }}" @selected($selectedGolongan === $kode)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('golongan_at')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Lokasi: dropdown kiri = Klaster, dropdown kanan = Wisata (menyesuaikan klaster) --}}
    <div>
        <label for="kode_klaster" class="form-label">
            {{ __('Klaster') }}
        </label>
        <select id="kode_klaster" class="form-control" required>
            <option value="">{{ __('-- Pilih Klaster --') }}</option>
            @foreach ($klasters as $k)
                <option value="{{ $k['kode'] }}" @selected($selectedKlaster === $k['kode'])>
                    {{ $k['kode'] }} - {{ $k['nama'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="location_id" class="form-label">
            {{ __('Lokasi Pemakaian') }}
        </label>
        <select id="location_id" name="location_id" class="form-control" required disabled>
            <option value="">{{ __('-- Pilih Wisata --') }}</option>
        </select>
        @error('location_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kategori: dropdown kiri = Kode Aktiva Tetap, dropdown kanan = Sub Jenis (menyesuaikan kiri) --}}
    <div>
        <label for="kode_aktiva_tetap" class="form-label">
            {{ __('Kode Aktiva Tetap') }}
        </label>
        <select id="kode_aktiva_tetap" class="form-control" required>
            <option value="">{{ __('-- Pilih Kode Aktiva Tetap --') }}</option>
            @foreach ($groups as $group)
                <option value="{{ $group['kode'] }}" @selected($selectedKode === $group['kode'])>
                    {{ $group['kode'] }} - {{ $group['jenis'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="category_id" class="form-label">
            {{ __('Sub Jenis') }}
        </label>
        <select id="category_id" name="category_id" class="form-control" required disabled>
            <option value="">{{ __('-- Pilih Sub Jenis --') }}</option>
        </select>
        @error('category_id')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
        <p id="kode-gabungan-preview" class="mt-1.5 text-sm text-gray-400"></p>
    </div>

    <div>
        <label for="tahun_perolehan" class="form-label">
            {{ __('Tahun Perolehan') }}
        </label>
        <input type="number" id="tahun_perolehan" name="tahun_perolehan" min="1990" max="{{ date('Y') }}"
            value="{{ old('tahun_perolehan', $item->tahun_perolehan ?? '') }}" required
            class="form-control" />
        @error('tahun_perolehan')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="masa_manfaat" class="form-label">
            {{ __('Masa Manfaat (tahun)') }}
        </label>
        <input type="number" id="masa_manfaat" name="masa_manfaat" min="1" max="50"
            value="{{ old('masa_manfaat', $item->masa_manfaat ?? '') }}" required
            class="form-control" />
        @error('masa_manfaat')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nilai_perolehan" class="form-label">
            {{ __('Nilai Perolehan (Rp)') }}
        </label>
        <input type="number" step="0.01" id="nilai_perolehan" name="nilai_perolehan"
            value="{{ old('nilai_perolehan', $item->nilai_perolehan ?? '') }}" required
            class="form-control" />
        @error('nilai_perolehan')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kondisi" class="form-label">
            {{ __('Kondisi') }}
        </label>
        <select id="kondisi" name="kondisi" class="form-control">
            <option value="">{{ __('-- Pilih Kondisi --') }}</option>
            @foreach (\App\Models\Item::KONDISI_LABELS as $kode => $label)
                <option value="{{ $kode }}" @selected(old('kondisi', $item->kondisi ?? '') == $kode)>
                    {{ $kode }} - {{ $label }}
                </option>
            @endforeach
        </select>
        @error('kondisi')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nomor Aktiva Tetap: hanya tampilan (tanpa name), nomor final dibuat server saat simpan --}}
    <div>
        <label for="nomor_aktiva_preview" class="form-label">
            {{ __('Nomor Aktiva Tetap') }}
        </label>
        <input type="text" id="nomor_aktiva_preview" readonly
            value="{{ $item->nomor_aktiva_tetap ?? '' }}"
            class="form-control bg-gray-50" />
        <p class="mt-1.5 text-sm text-gray-400">
            {{ __('Terbentuk otomatis. Nomor urut (XXXX) diberikan sistem saat disimpan.') }}
        </p>
        @error('nomor_aktiva_tetap')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tanggal_terima" class="form-label">
            {{ __('Tanggal Terima') }}
        </label>
        <input type="date" id="tanggal_terima" name="tanggal_terima"
            value="{{ old('tanggal_terima', isset($item->tanggal_terima) ? $item->tanggal_terima->format('Y-m-d') : '') }}" required
            class="form-control" />
        @error('tanggal_terima')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="btn btn-primary">
        {{ __('Simpan') }}
    </button>
</div>

<script>
    (function () {
        const groups = @json($groups);
        const klasters = @json($klasters);
        const selectedCategoryId = @json($selectedCategoryId);
        const selectedLocationId = @json($selectedLocationId);
        const nomorUrut = @json($item->nomor_urut ?? null);
        const golonganKode = @json(\App\Models\Item::GOLONGAN_KODE);

        const kodeEl     = document.getElementById('kode_aktiva_tetap');
        const subEl      = document.getElementById('category_id');
        const previewEl  = document.getElementById('kode-gabungan-preview');
        const klasterEl  = document.getElementById('kode_klaster');
        const wisataEl   = document.getElementById('location_id');
        const golonganEl = document.getElementById('golongan_at');
        const tahunEl    = document.getElementById('tahun_perolehan');
        const nomorEl    = document.getElementById('nomor_aktiva_preview');

        function updatePreview() {
            const opt = subEl.selectedOptions[0];
            previewEl.textContent = opt && opt.dataset.gabungan
                ? 'Kode gabungan (segmen ke-3 nomor aktiva): ' + opt.dataset.gabungan
                : '';
        }

        function updateNomor() {
            const urut = nomorUrut ? String(nomorUrut).padStart(4, '0') : 'XXXX';
            const tipe = golonganKode[golonganEl.value] || 'X';
            const kat  = (subEl.selectedOptions[0] && subEl.selectedOptions[0].dataset.gabungan) || 'XXXX';
            const lok  = (wisataEl.selectedOptions[0] && wisataEl.selectedOptions[0].dataset.gabungan) || 'XXX';
            const thn  = tahunEl.value || 'XXXX';
            nomorEl.value = [urut, tipe, kat, lok, thn].join('.');
        }

        function renderSubs(kode, pickedId) {
            subEl.innerHTML = '<option value="">-- Pilih Sub Jenis --</option>';

            const group = groups.find(function (g) { return g.kode === kode; });
            if (!group) {
                subEl.disabled = true;
                updatePreview();
                return;
            }

            group.subs.forEach(function (s) {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.sub_jenis + ' - ' + (s.keterangan || '');
                opt.dataset.gabungan = kode + s.sub_jenis;
                if (pickedId !== null && String(s.id) === String(pickedId)) opt.selected = true;
                subEl.appendChild(opt);
            });

            subEl.disabled = false;
            updatePreview();
        }

        function renderWisata(kodeKlaster, pickedId) {
            wisataEl.innerHTML = '<option value="">-- Pilih Wisata --</option>';

            const k = klasters.find(function (x) { return x.kode === kodeKlaster; });
            if (!k) {
                wisataEl.disabled = true;
                return;
            }

            k.wisata.forEach(function (w) {
                const opt = document.createElement('option');
                opt.value = w.id;
                opt.textContent = w.kode + ' - ' + w.nama;
                opt.dataset.gabungan = k.kode + w.kode;
                if (pickedId !== null && String(w.id) === String(pickedId)) opt.selected = true;
                wisataEl.appendChild(opt);
            });

            wisataEl.disabled = false;
        }

        kodeEl.addEventListener('change', function () {
            renderSubs(kodeEl.value, null);
        });
        subEl.addEventListener('change', updatePreview);

        klasterEl.addEventListener('change', function () {
            renderWisata(klasterEl.value, null);
        });

        // Preview nomor aktiva ikut berubah setiap ada perubahan di form
        klasterEl.form.addEventListener('change', updateNomor);
        klasterEl.form.addEventListener('input', updateNomor);

        // Saat edit atau setelah validasi gagal, pulihkan pilihan sebelumnya
        if (kodeEl.value) renderSubs(kodeEl.value, selectedCategoryId);
        if (klasterEl.value) renderWisata(klasterEl.value, selectedLocationId);
        updateNomor();
    })();
</script>