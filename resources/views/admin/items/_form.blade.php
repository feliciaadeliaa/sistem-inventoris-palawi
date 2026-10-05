@php
    $item = $item ?? null;

    $selectedCategoryId = old('category_id', $item->category_id ?? null);
    $selectedLocationId = old('location_id', $item->location_id ?? null);

    // Kelompokkan per Kode Aktiva Tetap untuk dropdown kiri dan kanan
    // (pastikan $categories di-load dengan ->with('golongan'))
    $groups = $categories->groupBy('kode_aktiva_tetap')->map(fn ($rows, $kode) => [
        'kode'  => (string) $kode,
        'jenis' => $rows->first()->jenis_aktiva_tetap,
        'subs'  => $rows->map(fn ($c) => [
            'id'           => $c->category_id,
            'sub_jenis'    => $c->sub_jenis,
            'keterangan'   => $c->keterangan_fungsi,
            'golongan'     => optional($c->golongan)->nama,
            'masa_manfaat' => optional($c->golongan)->masa_manfaat,
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

    $selectedAtIbat = old('at_ibat', $item->at_ibat ?? '');
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

    {{-- AT/IBAT: AT = digit 1, IBAT = digit 2 pada nomor aktiva --}}
    <div>
        <label for="at_ibat" class="form-label">
            {{ __('AT/IBAT') }}
        </label>
        <select id="at_ibat" name="at_ibat" class="form-control" required>
            <option value="">{{ __('-- Pilih AT/IBAT --') }}</option>
            @foreach (array_keys(\App\Models\Item::AT_IBAT_KODE) as $kode)
                <option value="{{ $kode }}" @selected($selectedAtIbat === $kode)>
                    {{ $kode }}
                </option>
            @endforeach
        </select>
        @error('at_ibat')
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

    {{-- Golongan AT & Masa Manfaat: otomatis dari Sub Jenis --}}
    <div>
        <label for="golongan_display" class="form-label">
            {{ __('Golongan AT') }}
        </label>
        <input type="text" id="golongan_display" readonly placeholder="Terisi otomatis dari Sub Jenis"
            class="form-control bg-gray-50" />
        <p id="golongan-warning" class="mt-1.5 text-sm text-error-500 hidden">
            {{ __('Sub jenis ini belum punya golongan AT. Atur dulu di menu Kategori.') }}
        </p>
    </div>

    <div>
        <label for="masa_manfaat" class="form-label">
            {{ __('Masa Manfaat (tahun)') }}
        </label>
        <input type="number" id="masa_manfaat" name="masa_manfaat" readonly placeholder="Terisi otomatis"
            value="{{ old('masa_manfaat', $item->masa_manfaat ?? '') }}"
            class="form-control bg-gray-50" />
        @error('masa_manfaat')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
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
        const atIbatKode = @json(\App\Models\Item::AT_IBAT_KODE);

        const kodeEl      = document.getElementById('kode_aktiva_tetap');
        const subEl       = document.getElementById('category_id');
        const previewEl   = document.getElementById('kode-gabungan-preview');
        const klasterEl   = document.getElementById('kode_klaster');
        const wisataEl    = document.getElementById('location_id');
        const atIbatEl    = document.getElementById('at_ibat');
        const tahunEl     = document.getElementById('tahun_perolehan');
        const nomorEl     = document.getElementById('nomor_aktiva_preview');
        const golonganEl  = document.getElementById('golongan_display');
        const masaEl      = document.getElementById('masa_manfaat');
        const warningEl   = document.getElementById('golongan-warning');

        function updatePreview() {
            const opt = subEl.selectedOptions[0];
            previewEl.textContent = opt && opt.dataset.gabungan
                ? 'Kode gabungan (segmen ke-3 nomor aktiva): ' + opt.dataset.gabungan
                : '';
        }

        // Golongan AT dan masa manfaat ikut sub jenis yang dipilih
        function updateGolongan() {
            const opt = subEl.selectedOptions[0];
            const adaSubJenis = opt && opt.value;

            golonganEl.value = adaSubJenis ? (opt.dataset.golongan || '') : '';
            masaEl.value     = adaSubJenis ? (opt.dataset.masa || '') : '';
            warningEl.classList.toggle('hidden', !(adaSubJenis && !opt.dataset.golongan));
        }

        function updateNomor() {
            const urut = nomorUrut ? String(nomorUrut).padStart(4, '0') : 'XXXX';
            const tipe = atIbatKode[atIbatEl.value] || 'X';
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
                updateGolongan();
                return;
            }

            group.subs.forEach(function (s) {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.sub_jenis + ' - ' + (s.keterangan || '');
                opt.dataset.gabungan = kode + s.sub_jenis;
                opt.dataset.golongan = s.golongan || '';
                opt.dataset.masa = (s.masa_manfaat !== null && s.masa_manfaat !== undefined) ? s.masa_manfaat : '';
                if (pickedId !== null && String(s.id) === String(pickedId)) opt.selected = true;
                subEl.appendChild(opt);
            });

            subEl.disabled = false;
            updatePreview();
            updateGolongan();
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
        subEl.addEventListener('change', function () {
            updatePreview();
            updateGolongan();
        });

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