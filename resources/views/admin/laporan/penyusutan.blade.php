@extends('layouts.app')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $periodeLabel = $mode === 'tahunan'
        ? 'Tahun ' . $tahun
        : $namaBulan[$bulan] . ' ' . $tahun;
@endphp

<div class="p-6" style="background:#F4F9F8; min-height:100vh;">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laporan Penyusutan Aset</h1>
        <p class="text-sm text-gray-500">
            Periode: {{ $periodeLabel }}
            ({{ $awal->format('d/m/Y') }} – {{ $akhir->format('d/m/Y') }})
        </p>

        @if ($status === 'berjalan')
            <span class="mt-2 inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                Berjalan – dihitung sampai {{ $hariIni->format('d/m/Y') }}
            </span>
        @elseif ($status === 'final')
            <span class="mt-2 inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                Final - berakhir pada {{ $akhir->format('d/m/Y') }}
            </span>
        @else
            <span class="mt-2 inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-gray-600">
                Periode belum dimulai
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.laporan.penyusutan') }}"
          x-data="{ mode: '{{ $mode }}' }"
          class="mb-6 flex flex-wrap items-end gap-4 rounded-xl bg-white p-4 shadow-sm">

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Mode</label>
            <select name="mode" x-model="mode" class="rounded-lg border-gray-300 text-sm">
                <option value="bulanan">Bulanan</option>
                <option value="tahunan">Tahunan</option>
            </select>
        </div>

        <div x-show="mode === 'bulanan'">
            <label class="mb-1 block text-sm font-medium text-gray-700">Bulan</label>
            <select name="bulan" class="rounded-lg border-gray-300 text-sm">
                @foreach ($namaBulan as $no => $nama)
                    <option value="{{ $no }}" @selected($no === $bulan)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}" min="1990" max="2100"
                   class="w-28 rounded-lg border-gray-300 text-sm">
        </div>

        <button type="submit"
                class="rounded-lg px-5 py-2 text-sm font-medium text-white"
                style="background: linear-gradient(103.98deg, #3DA597 0%, #00617A 100%);">
            Tampilkan
        </button>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">ID Item</th>
                    <th class="px-4 py-3">Nama Barang</th>
                    <th class="px-4 py-3">Jenis Aktiva</th>
                    <th class="px-4 py-3">No. Aktiva Tetap</th>
                    <th class="px-4 py-3">Tgl Terima</th>
                    <th class="px-4 py-3 text-right">Masa Manfaat</th>
                    <th class="px-4 py-3 text-right">Nilai Perolehan</th>
                    <th class="px-4 py-3 text-right">Penyusutan Periode</th>
                    <th class="px-4 py-3 text-right">Akumulasi</th>
                    <th class="px-4 py-3 text-right">Nilai Buku</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    @php $item = $row['item']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $item->item_id }}</td>
                        <td class="px-4 py-3">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-3">{{ $item->category->jenis_aktiva_tetap ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->nomor_aktiva_tetap ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->masa_manfaat }} th</td>
                        <td class="px-4 py-3 text-right">{{ $rp($item->nilai_perolehan) }}</td>
                        <td class="px-4 py-3 text-right">{{ $rp($row['penyusutan_periode']) }}</td>
                        <td class="px-4 py-3 text-right">{{ $rp($row['akumulasi']) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $rp($row['nilai_buku']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada aset pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if ($rows->isNotEmpty())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-right">Total</td>
                        <td class="px-4 py-3 text-right">{{ $rp($totals['nilai_perolehan']) }}</td>
                        <td class="px-4 py-3 text-right">{{ $rp($totals['penyusutan_periode']) }}</td>
                        <td class="px-4 py-3 text-right">{{ $rp($totals['akumulasi']) }}</td>
                        <td class="px-4 py-3 text-right">{{ $rp($totals['nilai_buku']) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection