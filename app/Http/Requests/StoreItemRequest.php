<?php
// app/Http/Requests/StoreItemRequest.php
namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Masa manfaat tidak diambil dari input user, tapi dari golongan AT milik sub jenis (menu Kategori).
    protected function prepareForValidation(): void
    {
        $golongan = Category::with('golongan')->find($this->input('category_id'))?->golongan;

        $this->merge(['masa_manfaat' => $golongan?->masa_manfaat]);
    }

    public function rules(): array
    {
        // nomor_aktiva_tetap dan nomor_urut sengaja tidak ada di sini:
        // keduanya dibuat server, jadi input dari user tidak akan pernah dipakai.
        return [
            'nama_barang'     => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'exists:categories,category_id'],
            'location_id'     => ['required', 'exists:locations,id'],
            'at_ibat'         => ['required', 'in:AT,IBAT'],
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
            'category_id.exists'    => 'Kategori tidak valid.',
            'location_id.exists'    => 'Lokasi tidak valid.',
            'at_ibat.required'      => 'AT/IBAT wajib dipilih.',
            'at_ibat.in'            => 'AT/IBAT harus AT atau IBAT.',
            'tahun_perolehan.max'   => 'Tahun perolehan tidak boleh melebihi tahun ini.',
            'masa_manfaat.required' => 'Sub jenis ini belum punya golongan AT. Atur dulu di menu Kategori.',
        ];
    }
}