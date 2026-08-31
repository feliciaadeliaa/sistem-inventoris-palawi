@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold text-white mb-6">Scan atau cari barang</h1>

    {{-- Area Kamera Scan QR --}}
    <div class="border-2 border-dashed border-gray-600 rounded-xl flex flex-col items-center justify-center py-16 mb-6 bg-gray-900/40">
        <div id="qr-reader" class="w-full max-w-xs"></div>
        <div id="qr-placeholder" class="flex flex-col items-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V6a2 2 0 012-2h2M4 16v2a2 2 0 002 2h2m8-16h2a2 2 0 012 2v2m-4 12h2a2 2 0 002-2v-2M8 12h8" />
            </svg>
            <span>Area kamera scan QR</span>
            <button id="btn-start-scan" type="button" class="mt-4 text-sm text-green-500 underline">
                Aktifkan kamera
            </button>
        </div>
    </div>

    <div class="flex items-center gap-4 mb-6">
        <div class="flex-1 border-t border-gray-700"></div>
        <span class="text-gray-500 text-sm">atau</span>
        <div class="flex-1 border-t border-gray-700"></div>
    </div>

    {{-- Search bar --}}
    <input
        type="text"
        id="search-barang"
        placeholder="Cari nama atau kode barang"
        class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-2 focus:ring-green-600"
        autocomplete="off"
    >
    <div id="search-results" class="mb-6 space-y-2"></div>

    {{-- Hasil scan / hasil pilih --}}
    <div id="hasil-scan" class="hidden border border-gray-700 rounded-xl p-5 bg-gray-900/40">
        <p class="text-gray-400 text-sm mb-2">Hasil scan</p>
        <h2 id="hasil-nama" class="text-white text-lg font-semibold"></h2>
        <p id="hasil-info" class="text-gray-400 text-sm mb-4"></p>

        @if(auth()->user()->role === 'admin')
            {{-- Admin: riwayat transaksi barang ini --}}
            <div id="riwayat-transaksi" class="mb-4 space-y-2"></div>

            <p class="text-gray-400 text-sm mb-2">Proses transaksi</p>
            <div class="flex flex-wrap gap-3">
                <button type="button" data-action="stock_in" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Stock In
                </button>
                <button type="button" data-action="stock_out" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Stock Out
                </button>
                <button type="button" data-action="mutasi" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Mutasi
                </button>
                <button type="button" data-action="perbaikan" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Perbaikan
                </button>
                <button type="button" data-action="kerusakan" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Kerusakan
                </button>
            </div>
        @else
            {{-- User: ajukan transaksi --}}
            <div class="flex flex-wrap gap-3">
                <button type="button" data-action="peminjaman" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Ajukan peminjaman
                </button>
                <button type="button" data-action="perbaikan" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Ajukan perbaikan
                </button>
                <button type="button" data-action="kerusakan" class="hasil-action-btn border border-gray-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800">
                    Lapor rusak
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = "{{ route('barang.cari') }}";
    const detailUrlTemplate = "{{ route('barang.detail', ['item_id' => '__ITEM_ID__']) }}";
    const isAdmin = @json(auth()->user()->role === 'admin');

    // Peta tombol aksi: berbeda untuk Admin (proses langsung) dan User (ajukan)
    const actionRoutes = isAdmin ? {
        stock_in: '/admin/stock-in',
        stock_out: '/admin/transaksi/stock-out',
        mutasi: '/admin/transaksi/mutasi',
        perbaikan: '/admin/transaksi/perbaikan',
        kerusakan: '/admin/transaksi/kerusakan',
    } : {
        peminjaman: '/transaksi/stock-out',
        perbaikan: '/ajukan/perbaikan',
        kerusakan: '/ajukan/kerusakan',
    };

    const statusLabel = {
        selesai: 'Selesai',
        menunggu_approval: 'Menunggu approval',
        disetujui: 'Disetujui',
        ditolak: 'Ditolak',
        dikembalikan: 'Dikembalikan',
    };

    const jenisLabel = {
        stock_in: 'Stock In',
        stock_out: 'Stock Out',
        mutasi: 'Mutasi',
        permintaan_perbaikan: 'Permintaan Perbaikan',
        laporan_kerusakan: 'Laporan Kerusakan',
    };

    const hasilScan = document.getElementById('hasil-scan');
    const hasilNama = document.getElementById('hasil-nama');
    const hasilInfo = document.getElementById('hasil-info');
    let currentItemId = null;

    function tampilkanHasil(item) {
        currentItemId = item.item_id;
        hasilNama.textContent = item.nama_barang;
        hasilInfo.textContent = `${item.item_id} · ${item.lokasi}`;
        hasilScan.classList.remove('hidden');
        document.getElementById('search-results').innerHTML = '';

        if (isAdmin) {
            renderRiwayat(item.transactions || []);
        }
    }

    function renderRiwayat(transactions) {
        const container = document.getElementById('riwayat-transaksi');
        if (!container) return;

        if (transactions.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">Belum ada riwayat transaksi.</p>';
            return;
        }

        container.innerHTML = transactions.map(t => `
            <div class="flex items-center justify-between bg-gray-800/60 rounded-lg px-3 py-2 text-sm">
                <div>
                    <span class="text-white font-medium">${jenisLabel[t.jenis_transaksi] || t.jenis_transaksi}</span>
                    <span class="block text-gray-400">${t.user} · ${t.tanggal}</span>
                </div>
                <span class="text-gray-300 text-xs border border-gray-600 rounded px-2 py-1">
                    ${statusLabel[t.status] || t.status}
                </span>
            </div>
        `).join('');
    }

    // --- Pencarian by nama/kode ---
    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');
    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(items => {
                    searchResults.innerHTML = '';
                    items.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 hover:bg-gray-800';
                        btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>
                                          <span class="block text-gray-400 text-sm">${item.item_id} · ${item.lokasi}</span>`;
                        btn.addEventListener('click', () => tampilkanHasil(item));
                        searchResults.appendChild(btn);
                    });
                })
                .catch(() => {
                    searchResults.innerHTML = '<p class="text-red-400 text-sm">Gagal mencari barang.</p>';
                });
        }, 300);
    });

    // --- Tombol aksi (peminjaman/perbaikan/lapor rusak) ---
    document.querySelectorAll('.hasil-action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!currentItemId) return;
            const base = actionRoutes[this.dataset.action];
            window.location.href = `${base}?item_id=${encodeURIComponent(currentItemId)}`;
        });
    });

    // --- Scan kamera QR ---
    const btnStartScan = document.getElementById('btn-start-scan');
    const qrPlaceholder = document.getElementById('qr-placeholder');
    let html5QrCode;

    btnStartScan.addEventListener('click', function () {
        qrPlaceholder.classList.add('hidden');
        html5QrCode = new Html5Qrcode('qr-reader');

        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 220 },
            (decodedText) => {
                html5QrCode.stop().then(() => {
                    document.getElementById('qr-reader').innerHTML = '';
                    qrPlaceholder.classList.remove('hidden');
                });

                fetch(detailUrlTemplate.replace('__ITEM_ID__', encodeURIComponent(decodedText)))
                    .then(res => {
                        if (!res.ok) throw new Error('Barang tidak ditemukan');
                        return res.json();
                    })
                    .then(item => tampilkanHasil(item))
                    .catch(() => alert('Barang tidak ditemukan untuk kode: ' + decodedText));
            },
            () => { /* diabaikan: dipanggil terus saat belum ada QR terdeteksi */ }
        ).catch(() => {
            alert('Tidak bisa mengakses kamera. Pastikan izin kamera sudah diberikan.');
            qrPlaceholder.classList.remove('hidden');
        });
    });
});
</script>
@endpush
@endsection