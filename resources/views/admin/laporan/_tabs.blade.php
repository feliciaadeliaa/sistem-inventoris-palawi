@php
    $tabs = [
        'riwayat-barang' => ['label' => 'Riwayat per Barang', 'route' => 'admin.laporan.riwayat-barang.index'],
        'peminjaman-aktif' => ['label' => 'Peminjaman Aktif/Terlambat', 'route' => 'admin.laporan.peminjaman-aktif.index'],
        'stok' => ['label' => 'Stok per Kategori/Lokasi', 'route' => null],
        'perbaikan' => ['label' => 'Permintaan Perbaikan', 'route' => 'admin.laporan.permintaan-perbaikan.index'],
        'fasilitas' => ['label' => 'Pengurangan Fasilitas', 'route' => 'admin.laporan.pengurangan-fasilitas.index'],
    ];
@endphp

<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <nav class="flex flex-wrap gap-6 -mb-px">
        @foreach ($tabs as $key => $tab)
            @if ($tab['route'])
                <a href="{{ route($tab['route']) }}"
                    class="pb-3 text-sm font-medium border-b-2 {{ $active === $key ? 'text-brand-500 border-brand-500' : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-200' }}">
                    {{ $tab['label'] }}
                </a>
            @else
                <span class="pb-3 text-sm font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed" title="Segera hadir">
                    {{ $tab['label'] }}
                </span>
            @endif
        @endforeach
    </nav>
</div>