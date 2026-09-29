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
        // nomor_aktiva_tetap dan nomor_urut sengaja tidak ada di sini:
        // keduanya dibuat server, jadi input dari user tidak akan pernah dipakai.
        return [
            'nama_barang'     => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'exists:categories,category_id'],
            'location_id'     => ['required', 'exists:locations,id'],
            'golongan_at'     => ['required', 'in:I,II'],
            'tahun_perolehan' => ['required', 'digits:4', 'integer', 'min:1990', 'max:' . date('Y')],
            'masa_manfaat'    => ['required', 'integer', 'min:1', 'max:50'],
            'nilai_perolehan' => ['required', 'numeric', 'min:0'],
            'kondisi'         => ['required', 'in:B,BPR,RB,RSS'],
            'tanggal_terima'  => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists'   => 'Kategori tidak valid.',
            'location_id.exists'   => 'Lokasi tidak valid.',
            'golongan_at.required' => 'Golongan AT wajib dipilih.',
            'golongan_at.in'       => 'Golongan AT harus I (AT) atau II (IBAT).',
            'tahun_perolehan.max'  => 'Tahun perolehan tidak boleh melebihi tahun ini.',
        ];
    }
}