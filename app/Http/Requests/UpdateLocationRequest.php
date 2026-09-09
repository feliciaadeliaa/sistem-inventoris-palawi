<?php
// app/Http/Requests/UpdateLocationRequest.php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateLocationRequest extends StoreLocationRequest
{
    public function rules(): array
    {
        return [
            'nama_lokasi' => ['required', 'string', 'max:255'],
            'kode_lokasi' => [
                'nullable', 'string', 'max:50',
                Rule::unique('locations', 'kode_lokasi')->ignore($this->route('location')),
            ],
            'wilayah' => ['nullable', 'string', 'max:255'],
            'unit_bisnis' => ['nullable', 'string', 'max:255'],
        ];
    }
}