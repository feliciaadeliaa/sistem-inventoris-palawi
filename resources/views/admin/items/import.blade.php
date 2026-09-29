@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">Import Data Barang</h2>
            <p class="page-desc">
                Upload file Excel untuk menambahkan banyak aset sekaligus. Nomor aktiva tetap dibuat otomatis oleh sistem.
            </p>
        </div>

        <a href="{{ route('barang.index') }}" class="btn btn-outline">
            &larr; Kembali
        </a>
    </div>

    {{-- Error validasi per baris --}}
    @if ($errors->has('file'))
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg text-sm">
            <p class="font-semibold mb-2">Import dibatalkan, tidak ada data yang disimpan. Perbaiki baris berikut:</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->get('file') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Form upload --}}
        <div class="card card-pad lg:col-span-1">
            <h3 class="font-semibold text-gray-800 mb-1">1. Siapkan file</h3>
            <p class="text-sm text-gray-500 mb-3">
                Gunakan template agar nama kolomnya sesuai.
            </p>
            <a href="{{ route('barang.import.template') }}" class="btn btn-outline mb-6 inline-block">
                Download Template
            </a>

            <h3 class="font-semibold text-gray-800 mb-1">2. Upload file</h3>
            <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required class="form-control mb-3">
                <p class="text-xs text-gray-400 mb-4">Format .xlsx atau .xls, maksimal 5 MB.</p>

                <button type="submit" class="btn btn-primary">
                    Import Sekarang
                </button>
            </form>
        </div>

        {{-- Panduan kolom --}}
        <div class="card card-pad lg:col-span-2">
            <h3 class="font-semibold text-gray-800 mb-3">Panduan kolom</h3>

            <div class="overflow-x-auto">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>nama_barang</td><td>Ya</td><td>Nama aset</td></tr>
                        <tr><td>kode_aktiva_tetap</td><td>Ya</td><td>2 digit, sesuai menu Kategori (contoh: 09)</td></tr>
                        <tr><td>sub_jenis</td><td>Ya</td><td>2 digit, sesuai menu Kategori (contoh: 01)</td></tr>
                        <tr><td>tipe_aset</td><td>Ya</td><td>AT atau IBAT</td></tr>
                        <tr><td>kode_klaster</td><td>Ya</td><td>1 digit, sesuai menu Lokasi</td></tr>
                        <tr><td>kode_lokasi</td><td>Ya</td><td>2 digit kode wisata, sesuai menu Lokasi</td></tr>
                        <tr><td>tahun_perolehan</td><td>Ya</td><td>4 digit (contoh: 2026)</td></tr>
                        <tr><td>tanggal_terima</td><td>Ya</td><td>Format YYYY-MM-DD</td></tr>
                        <tr><td>nilai_perolehan</td><td>Ya</td><td>Angka tanpa titik/koma (contoh: 12500000)</td></tr>
                        <tr><td>kondisi</td><td>Tidak</td><td>B, BPR, atau RB. Kosong dianggap B</td></tr>
                        <tr><td>golongan_at</td><td>Tidak*</td><td>Kosong = diambil dari kategori</td></tr>
                        <tr><td>masa_manfaat</td><td>Tidak*</td><td>Kosong = diambil dari kategori</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-500 mt-3">
                * Kalau kategori belum punya golongan AT / masa manfaat, kolom ini wajib diisi di Excel.
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Jika ada satu baris yang salah, seluruh import dibatalkan supaya data tidak masuk setengah-setengah.
            </p>
        </div>
    </div>
@endsection