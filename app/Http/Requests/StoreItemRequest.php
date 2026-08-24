<?php
// app/Http/Requests/StoreItemRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'      => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,category_id'],
            'location_id'      => ['required', 'exists:locations,id'],
            'golongan_at' => ['required', 'string', 'max:10'],
            'tahun_perolehan'  => ['required', 'digits:4', 'integer', 'min:1990', 'max:' . date('Y')],
            'masa_manfaat'     => ['required', 'integer', 'min:1', 'max:50'],
            'nilai_perolehan'  => ['required', 'numeric', 'min:0'],
            'kondisi'          => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'tanggal_terima'   => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori tidak valid.',
            'location_id.exists' => 'Lokasi tidak valid.',
            'tahun_perolehan.max' => 'Tahun perolehan tidak boleh melebihi tahun ini.',
        ];
    }
}