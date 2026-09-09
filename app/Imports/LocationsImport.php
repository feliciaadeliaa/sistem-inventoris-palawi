<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class LocationsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $imported = 0;
    public $skipped = 0;

public function model(array $row): Model|array|null
{
    \Log::info('IMPORT ROW KEYS: ' . json_encode($row));

    $kodeLokasi = $row['code'] ?? null;

        if ($kodeLokasi && Location::where('kode_lokasi', $kodeLokasi)->exists()) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        return new Location([
            'kode_lokasi' => $kodeLokasi,
            'nama_lokasi' => $row['nama'] ?? '',
            'wilayah'     => $row['wilayah'] ?? null,
            'unit_bisnis' => $row['unit_bisnis'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
        ];
    }
}