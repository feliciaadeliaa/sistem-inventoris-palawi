<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['1', 'KLASTER MALANG', [
                ['01', 'COBAN RONDO'],
                ['02', 'COBAN RAIS'],
                ['03', 'COBAN TALUN'],
                ['04', 'DENDY SKY VIEW'],
                ['05', 'KANTOR KLASTER MALANG'],
            ]],
            ['2', 'KLASTER PATRA', [
                ['06', 'PADUSAN'],
                ['07', 'DLUNDUNG'],
                ['08', 'KAKEK BODO'],
                ['09', 'PUTHUK TRUNO'],
                ['10', 'FORESTA RESORT TRETES'],
                ['11', 'MADAKARIPURA'],
                ['12', 'KANTOR KLASTER PATRA'],
            ]],
            ['3', 'KLASTER LAWU', [
                ['13', 'VANAPRASTHA'],
                ['14', 'THE LAWU PARK'],
                ['15', 'SAKURA HILLS'],
                ['16', 'EMBUN LAWU'],
                ['17', 'MONGKRANG VIEW'],
                ['18', 'CURUG TEJO ASMORO'],
                ['19', 'MOJOSEMI'],
                ['20', 'SRAMBANG'],
                ['21', 'PONDOK PUSPA'],
                ['22', 'PINEA FOREST MANGLI'],
                ['23', 'GONOHARJO'],
                ['24', 'G TELOMOYO VIA ARSAL'],
                ['25', 'G TELOMOYO VIA WATU TUMPENG'],
                ['26', 'TELOMOYO VIA DALANGAN'],
                ['27', 'TELOMOYO VIA PAGERGEDOG'],
                ['28', 'PINUSIA PARK'],
                ['29', 'GUNUNG ANDONG VIA PENDEM'],
                ['30', 'GUNUNG ANDONG VIA SAWIT'],
                ['31', 'GUNUNG ANDONG VIA GOGIK'],
                ['32', 'SOWAN'],
                ['33', 'NGANGET'],
                ['34', 'REST AREA 626 A'],
                ['35', 'REST AREA 626 B'],
                ['36', 'KANTOR KLASTER LAWU'],
            ]],
            ['4', 'KLASTER BANYUWANGI - JEMBER', [
                ['37', 'PULAU MERAH'],
                ['38', 'TANJUNG PAPUMA'],
                ['39', 'WEDI IRENG'],
                ['40', 'GRAJAGAN'],
                ['41', 'DE DJAWATAN'],
                ['42', 'KANTOR KLASTER BANYUWANGI - JEMBER'],
            ]],
            ['5', 'KANTOR ABWWT', [
                ['43', 'KANTOR ABWWT'],
            ]],
        ];

        foreach ($data as [$kodeKlaster, $namaKlaster, $wisataList]) {
            foreach ($wisataList as [$kodeLokasi, $namaWisata]) {
                Location::create([
                    'kode_unit_bisnis' => $kodeKlaster,
                    'unit_bisnis'      => $namaKlaster,
                    'kode_lokasi'      => $kodeLokasi,
                    'nama_wisata'      => $namaWisata,
                ]);
            }
        }
    }
}