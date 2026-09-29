<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $klaster = $this->input('kode_unit_bisnis');

        return [
            'kode_unit_bisnis' => ['required', 'digits:1'],

            'unit_bisnis' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($klaster) {
                    // Satu kode klaster harus selalu punya nama yang sama
                    $beda = Location::where('kode_unit_bisnis', $klaster)
                        ->where('unit_bisnis', '!=', $value)
                        ->exists();

                    if ($beda) {
                        $fail('Nama klaster untuk kode ini harus sama dengan data yang sudah ada.');
                    }
                },
            ],

            'kode_lokasi' => ['required', 'digits:2', 'unique:locations,kode_lokasi'],
            'nama_wisata' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_unit_bisnis.digits' => 'Kode klaster harus 1 digit angka (contoh: 1).',
            'kode_lokasi.digits'      => 'Kode lokasi harus 2 digit angka (contoh: 01).',
            'kode_lokasi.unique'      => 'Kode lokasi ini sudah dipakai.',
        ];
    }
}