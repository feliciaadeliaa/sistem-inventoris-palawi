<?php

namespace App\Services;

use App\Models\Item;
use Carbon\Carbon;

class PenyusutanService
{
    /** Akumulasi penyusutan sampai (dan termasuk) tanggal acuan. Residu = 0. */
    public function akumulasi(Item $item, Carbon $tglAcuan): float
    {
        $harga = (float) $item->nilai_perolehan;
        $umur  = (int) $item->masa_manfaat;   // dari accessor (golongan)

        if ($harga <= 0 || $umur <= 0 || !$item->tanggal_terima) {
            return 0;
        }

        $mulai = Carbon::parse($item->tanggal_terima)->startOfDay();
        $acuan = $tglAcuan->copy()->startOfDay();

        // REALTIME: penyusutan tidak boleh melewati hari ini
        $hariIni = now()->startOfDay();
        if ($acuan->gt($hariIni)) {
            $acuan = $hariIni;
        }

        if ($acuan->lt($mulai)) {
            return 0;
        }

        $totalHari    = (int) $mulai->diffInDays($mulai->copy()->addYearsNoOverflow($umur));
        $hariBerjalan = min($totalHari, (int) $mulai->diffInDays($acuan) + 1);

        return $harga * $hariBerjalan / $totalHari;
    }

    public function nilaiBuku(Item $item, Carbon $tglAcuan): float
    {
        return (float) $item->nilai_perolehan - $this->akumulasi($item, $tglAcuan);
    }

    /** Satu baris laporan untuk periode [awal, akhir]. */
    public function baris(Item $item, Carbon $awal, Carbon $akhir): array
    {
        $akumAkhir = $this->akumulasi($item, $akhir);
        $akumAwal  = $this->akumulasi($item, $awal->copy()->subDay());

        return [
            'item'               => $item,
            'penyusutan_periode' => $akumAkhir - $akumAwal,
            'akumulasi'          => $akumAkhir,
            'nilai_buku'         => (float) $item->nilai_perolehan - $akumAkhir,
        ];
    }
}