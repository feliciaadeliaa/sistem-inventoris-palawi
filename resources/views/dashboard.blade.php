@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <p class="page-desc">
            Selamat datang kembali, {{ auth()->user()->name }}.
        </p>
    </div>

    @if ($role === 'admin')
        {{-- ===== STAT CARDS ADMIN ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <div class="card card-pad">
                <p class="stat-label">Total Barang</p>
                <p class="stat-value stat-value-brand">{{ $stats['total_barang'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Tersedia</p>
                <p class="stat-value stat-value-green">{{ $stats['barang_tersedia'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Dipinjam</p>
                <p class="stat-value stat-value-blue">{{ $stats['barang_dipinjam'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Nonaktif</p>
                <p class="stat-value stat-value-gray">{{ $stats['barang_nonaktif'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Peminjaman Menunggu</p>
                <p class="stat-value stat-value-amber">{{ $stats['stock_out_menunggu'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Mutasi Menunggu</p>
                <p class="stat-value stat-value-amber">{{ $stats['mutasi_menunggu'] }}</p>
            </div>
        </div>

        {{-- ===== CHARTS ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="card card-pad lg:col-span-2">
                <h3 class="panel-title">Transaksi 6 Bulan Terakhir</h3>
                <canvas id="chartBulan" height="100"></canvas>
            </div>
            <div class="card card-pad">
                <h3 class="panel-title">Kondisi Barang</h3>
                <canvas id="chartKondisi" height="220"></canvas>
            </div>
        </div>

        {{-- ===== JATUH TEMPO + RIWAYAT ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="card card-pad lg:col-span-2">
                <h3 class="panel-title">Aktivitas Terbaru</h3>
                <div class="space-y-3">
                    @forelse ($recentActivities as $trx)
                        <div class="list-row">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $trx->user->name ?? '-' }} &bull; {{ $trx->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="badge badge-slate">
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <div class="card card-pad">
                <h3 class="panel-title">Jatuh Tempo Pengembalian</h3>
                <div class="space-y-3">
                    @forelse ($jatuhTempo as $trx)
                        <div class="list-row-block">
                            <p class="text-sm font-medium text-gray-800">{{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $trx->user->name ?? '-' }} &bull;
                                <span class="{{ $trx->tanggal_kembali_estimasi->isPast() ? 'text-error-600 font-semibold' : '' }}">
                                    {{ $trx->tanggal_kembali_estimasi->format('d M Y') }}
                                </span>
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada yang jatuh tempo.</p>
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
                        { label: 'Stock In', data: @json($bulanChart['stock_in']), backgroundColor: '#26d185' },
                        { label: 'Stock Out', data: @json($bulanChart['stock_out']), backgroundColor: '#0ba5ec' },
                        { label: 'Mutasi', data: @json($bulanChart['mutasi']), backgroundColor: '#fdb022' },
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
                        backgroundColor: ['#26d185', '#fdb022', '#fd853a', '#f97066']
                    }]
                },
                options: { responsive: true }
            });
        </script>
        @endpush

    @elseif ($role === 'gm')
        {{-- ===== STAT CARDS GM ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card card-pad">
                <p class="stat-label">Peminjaman Menunggu</p>
                <p class="stat-value stat-value-amber">{{ $stats['stock_out_menunggu'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Mutasi Menunggu</p>
                <p class="stat-value stat-value-amber">{{ $stats['mutasi_menunggu'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Total Disetujui</p>
                <p class="stat-value stat-value-green">{{ $stats['total_disetujui'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Total Ditolak</p>
                <p class="stat-value stat-value-red">{{ $stats['total_ditolak'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="card card-pad lg:col-span-2">
                <h3 class="panel-title">Approval 6 Bulan Terakhir</h3>
                <canvas id="chartApproval" height="100"></canvas>
            </div>
            <div class="card card-pad">
                <h3 class="panel-title">Menunggu Approval Tertua</h3>
                <div class="space-y-3">
                    @forelse ($pendingList as $trx)
                        <div class="list-row-block">
                            <p class="text-sm font-medium text-gray-800">{{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $trx->user->name ?? '-' }} &bull; {{ $trx->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada yang menunggu.</p>
                    @endforelse
                </div>
                <a href="{{ route('gm.approval.index') }}" class="inline-block mt-3 text-sm text-brand-700 hover:underline">
                    Lihat semua approval &rarr;
                </a>
            </div>
        </div>

        <div class="card card-pad">
            <h3 class="panel-title">Riwayat Approval Saya</h3>
            <div class="space-y-3">
                @forelse ($recentActivities as $trx)
                    <div class="list-row">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $trx->jenis_transaksi }} — {{ $trx->item->nama_barang ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $trx->user->name ?? '-' }} &bull; {{ $trx->gm_approved_at?->diffForHumans() }}</p>
                        </div>
                        <span class="badge {{ $trx->status === 'disetujui' ? 'badge-green' : 'badge-red' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada riwayat approval.</p>
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
                        { label: 'Disetujui', data: @json($bulanChart['approved']), borderColor: '#26d185', backgroundColor: '#26d18533', tension: 0.3 },
                        { label: 'Ditolak', data: @json($bulanChart['rejected']), borderColor: '#f97066', backgroundColor: '#f9706633', tension: 0.3 },
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        </script>
        @endpush

    @else
        {{-- ===== DASHBOARD USER / SENIOR ANALIS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card card-pad">
                <p class="stat-label">Menunggu Approval</p>
                <p class="stat-value stat-value-amber">{{ $stats['menunggu'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Disetujui / Dipinjam</p>
                <p class="stat-value stat-value-blue">{{ $stats['disetujui'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Ditolak</p>
                <p class="stat-value stat-value-red">{{ $stats['ditolak'] }}</p>
            </div>
            <div class="card card-pad">
                <p class="stat-label">Sudah Dikembalikan</p>
                <p class="stat-value stat-value-green">{{ $stats['dikembalikan'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="card card-pad">
                <h3 class="panel-title">Status Pengajuan Saya</h3>
                <canvas id="chartStatus" height="220"></canvas>
            </div>

            <div class="card card-pad lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="panel-title mb-0">Riwayat Pengajuan Terbaru</h3>
                    <a href="{{ route('barang.scan') }}" class="btn btn-sm btn-primary">
                        + Ajukan Peminjaman
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentActivities as $trx)
                        <div class="list-row">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $trx->item->nama_barang ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $trx->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="badge badge-slate">
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada pengajuan.</p>
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
                        backgroundColor: ['#fdb022', '#0ba5ec', '#26d185', '#f97066', '#7a5af8']
                    }]
                },
                options: { responsive: true }
            });
        </script>
        @endpush
    @endif
@endsection