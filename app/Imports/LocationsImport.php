<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;

class LocationsImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public int $imported = 0;
    public int $updated = 0;

    public function collection(Collection $rows)
    {
        $seen = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2; // baris 1 = header
            $raw = $row->toArray();

            $data = [
                'kode_unit_bisnis' => $this->pad($raw['kode_unit_bisnis'] ?? null, 1),
                'unit_bisnis'      => trim((string) ($raw['unit_bisnis'] ?? '')),
                'kode_lokasi'      => $this->pad($raw['kode_lokasi'] ?? null, 2),
                'nama_wisata'      => trim((string) ($raw['nama_wisata'] ?? '')),
            ];

            // Lewati baris kosong
            if (!array_filter($data, fn ($v) => $v !== null && $v !== '')) {
                continue;
            }

            $v = Validator::make($data, [
                'kode_unit_bisnis' => ['required', 'digits:1'],
                'unit_bisnis'      => ['required', 'max:255'],
                'kode_lokasi'      => ['required', 'digits:2'],
                'nama_wisata'      => ['required', 'max:255'],
            ]);

            if ($v->fails()) {
                foreach ($v->errors()->messages() as $attr => $msgs) {
                    $this->onFailure(new Failure($line, $attr, $msgs, $raw));
                }
                continue;
            }

            $kode = $data['kode_lokasi'];

            // Baris kembar persis diabaikan, kembar tapi beda isi dianggap error
            if (isset($seen[$kode])) {
                if ($seen[$kode] !== $data) {
                    $this->onFailure(new Failure($line, 'kode_lokasi',
                        ["kode lokasi {$kode} muncul lagi dengan isi berbeda"], $raw));
                }
                continue;
            }
            $seen[$kode] = $data;

            // Satu kode klaster harus selalu punya nama yang sama
            $bentrok = Location::where('kode_unit_bisnis', $data['kode_unit_bisnis'])
                ->where('kode_lokasi', '!=', $kode)
                ->where('unit_bisnis', '!=', $data['unit_bisnis'])
                ->exists();

            if ($bentrok) {
                $this->onFailure(new Failure($line, 'unit_bisnis',
                    ["nama klaster untuk kode {$data['kode_unit_bisnis']} berbeda dengan data yang sudah ada"], $raw));
                continue;
            }

            // Klaster lokasi yang sudah dipakai barang tidak boleh berubah
            $ada = Location::where('kode_lokasi', $kode)->first();

            if ($ada && $ada->kode_unit_bisnis !== $data['kode_unit_bisnis'] && $ada->items()->exists()) {
                $this->onFailure(new Failure($line, 'kode_unit_bisnis',
                    ["lokasi {$kode} sudah dipakai barang, kode klasternya tidak boleh diubah"], $raw));
                continue;
            }

            $loc = Location::updateOrCreate(['kode_lokasi' => $kode], $data);

            $loc->wasRecentlyCreated ? $this->imported++ : $this->updated++;
        }
    }

    // "1" -> "01", 43.0 -> "43", "01" -> "01"
    private function pad($value, int $length): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value)) {
            $value = (int) $value;
        }

        return str_pad(trim((string) $value), $length, '0', STR_PAD_LEFT);
    }
}