<?php
// app/Http/Requests/StoreLocationRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lokasi' => ['required', 'string', 'max:255'],
            'kode_lokasi' => ['nullable', 'string', 'max:50', 'unique:locations,kode_lokasi'],
            'wilayah' => ['nullable', 'string', 'max:255'],
            'unit_bisnis' => ['nullable', 'string', 'max:255'],
        ];
    }
}