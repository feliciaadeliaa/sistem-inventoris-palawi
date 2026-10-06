@php
    $tabs = [
        'riwayat-barang' => ['label' => 'Riwayat per Barang', 'route' => 'admin.laporan.riwayat-barang.index'],
        'peminjaman-aktif' => ['label' => 'Peminjaman Aktif/Terlambat', 'route' => 'admin.laporan.peminjaman-aktif.index'],
        'perbaikan' => ['label' => 'Permintaan Perbaikan', 'route' => 'admin.laporan.permintaan-perbaikan.index'],
        'fasilitas' => ['label' => 'Pengurangan Fasilitas', 'route' => 'admin.laporan.pengurangan-fasilitas.index'],
    ];
@endphp

<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    {{-- Satu baris, bisa digeser ke samping kalau tidak muat. Tab aktif otomatis digulung ke tengah saat halaman dibuka. --}}
    <nav x-data
        x-init="$nextTick(() => {
            const a = $el.querySelector('[aria-current=page]');
            if (a) $el.scrollLeft = a.offsetLeft - ($el.clientWidth - a.offsetWidth) / 2;
        })"
        class="relative flex flex-nowrap gap-6 -mb-px overflow-x-auto overflow-y-hidden whitespace-nowrap [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
                @if ($active === $key) aria-current="page" @endif
                class="shrink-0 pb-3 text-sm font-medium border-b-2 {{ $active === $key ? 'text-brand-500 border-brand-500' : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-200' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>