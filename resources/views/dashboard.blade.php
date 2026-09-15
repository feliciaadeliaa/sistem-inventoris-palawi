@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Selamat datang kembali, {{ auth()->user()->name }}.
        </p>
    </div>

    @if ($role === 'admin')
        {{-- ===== STAT CARDS ADMIN ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Barang</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_barang'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Tersedia</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['barang_tersedia'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['barang_dipinjam'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
                <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['barang_nonaktif'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Peminjaman Menunggu</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['stock_out_menunggu'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Mutasi Menunggu</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['mutasi_menunggu'] }}</p>
            </div>
        </div>

        {{-- ===== CHARTS ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Transaksi 6 Bulan Terakhir</h3>
                <canvas id="chartBulan" height="100"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Kondisi Barang</h3>
                <canvas id="chartKondisi" height="220"></canvas>
            </div>
        </div>

        {{-- ===== JATUH TEMPO + RIWAYAT ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-3">
                    @forelse ($recentActivities as $trx)
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $trx->user->name ?? '-' }} &bull; {{ $trx->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Jatuh Tempo Pengembalian</h3>
                <div class="space-y-3">
                    @forelse ($jatuhTempo as $trx)
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $trx->user->name ?? '-' }} &bull;
                                <span class="{{ $trx->tanggal_kembali_estimasi->isPast() ? 'text-red-500 font-semibold' : '' }}">
                                    {{ $trx->tanggal_kembali_estimasi->format('d M Y') }}
                                </span>
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada yang jatuh tempo.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('chartBulan'), {
                type: 'bar',
                data: {
                    labels: @json($bulanChart['labels']),
                    datasets: [
                        { label: 'Stock In', data: @json($bulanChart['stock_in']), backgroundColor: '#22c55e' },
                        { label: 'Stock Out', data: @json($bulanChart['stock_out']), backgroundColor: '#3b82f6' },
                        { label: 'Mutasi', data: @json($bulanChart['mutasi']), backgroundColor: '#eab308' },
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });

            new Chart(document.getElementById('chartKondisi'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($kondisiChart->toArray())) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($kondisiChart->toArray())) !!},
                        backgroundColor: ['#22c55e', '#eab308', '#f97316', '#ef4444']
                    }]
                },
                options: { responsive: true }
            });
        </script>
        @endpush

    @elseif ($role === 'gm')
        {{-- ===== STAT CARDS GM ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Peminjaman Menunggu</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['stock_out_menunggu'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Mutasi Menunggu</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['mutasi_menunggu'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Disetujui</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['total_disetujui'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Ditolak</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['total_ditolak'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm lg:col-span-2">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Approval 6 Bulan Terakhir</h3>
                <canvas id="chartApproval" height="100"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Menunggu Approval Tertua</h3>
                <div class="space-y-3">
                    @forelse ($pendingList as $trx)
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $trx->user->name ?? '-' }} &bull; {{ $trx->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada yang menunggu.</p>
                    @endforelse
                </div>
                <a href="{{ route('gm.approval.index') }}" class="inline-block mt-3 text-sm text-brand-500 hover:underline">
                    Lihat semua approval &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Riwayat Approval Saya</h3>
            <div class="space-y-3">
                @forelse ($recentActivities as $trx)
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $trx->user->name ?? '-' }} &bull; {{ $trx->gm_approved_at?->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded {{ $trx->status === 'disetujui' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat approval.</p>
                @endforelse
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('chartApproval'), {
                type: 'line',
                data: {
                    labels: @json($bulanChart['labels']),
                    datasets: [
                        { label: 'Disetujui', data: @json($bulanChart['approved']), borderColor: '#22c55e', backgroundColor: '#22c55e33', tension: 0.3 },
                        { label: 'Ditolak', data: @json($bulanChart['rejected']), borderColor: '#ef4444', backgroundColor: '#ef444433', tension: 0.3 },
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        </script>
        @endpush

    @else
        {{-- ===== DASHBOARD USER / SENIOR ANALIS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Menunggu Approval</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['menunggu'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Disetujui / Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['disetujui'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Ditolak</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['ditolak'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400">Sudah Dikembalikan</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['dikembalikan'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Status Pengajuan Saya</h3>
                <canvas id="chartStatus" height="220"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Riwayat Pengajuan Terbaru</h3>
                    <a href="{{ route('barang.scan') }}" class="text-sm bg-brand-500 text-white px-3 py-1.5 rounded hover:bg-brand-600">
                        + Ajukan Peminjaman
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentActivities as $trx)
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $trx->item->nama_barang ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $trx->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pengajuan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('chartStatus'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($statusChart->toArray())) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($statusChart->toArray())) !!},
                        backgroundColor: ['#eab308', '#3b82f6', '#22c55e', '#ef4444', '#a855f7']
                    }]
                },
                options: { responsive: true }
            });
        </script>
        @endpush
    @endif
@endsection