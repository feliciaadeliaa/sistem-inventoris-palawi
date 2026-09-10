<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class LocationsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $imported = 0;
    public $updated = 0;

    public function model(array $row): Model|array|null
    {
        $kodeLokasi = $row['code'] ?? null;

        $data = [
            'kode_lokasi'     => $kodeLokasi,
            'nama_lokasi'     => $row['nama'] ?? '',
            'wilayah'         => $row['wilayah'] ?? null,
            'unit_bisnis'     => $row['unit_bisnis'] ?? null,
            'sub_unit_bisnis' => $row['sub_unit_bisnis'] ?? null,
        ];

        if ($kodeLokasi) {
            $existing = Location::where('kode_lokasi', $kodeLokasi)->first();

            if ($existing) {
                $existing->update($data);
                $this->updated++;
                return null;
            }
        }

        $this->imported++;

        return new Location($data);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
        ];
    }
}