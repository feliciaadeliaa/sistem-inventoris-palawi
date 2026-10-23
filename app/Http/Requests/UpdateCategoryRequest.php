<?php
// app/Http/Requests/UpdateCategoryRequest.php
namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Kode Aktiva Tetap dan Jenis selalu terkunci (membentuk nomor aktiva).
        $rules = [
            'keterangan_fungsi' => ['nullable', 'string', 'max:255'],
        ];

        $category = $this->route('category');

        // Kategori belum dipakai aset: Sub Jenis dan Golongan boleh diubah
        if ($category && $category->subJenisBolehDiubah()) {
            $rules['sub_jenis'] = [
                'nullable',
                'digits:2',
                Rule::unique('categories', 'sub_jenis')
                    ->where('kode_aktiva_tetap', $category->kode_aktiva_tetap)
                    ->ignore($category->category_id, 'category_id'),
                function ($attribute, $value, $fail) use ($category) {
                    // Cegah dua baris "tanpa sub jenis" untuk kode yang sama
                    if (filled($value)) {
                        return;
                    }

                    $ada = Category::where('kode_aktiva_tetap', $category->kode_aktiva_tetap)
                        ->whereNull('sub_jenis')
                        ->where('category_id', '!=', $category->category_id)
                        ->exists();

                    if ($ada) {
                        $fail('Jenis ini sudah punya baris tanpa sub jenis. Isi Sub Jenis, atau hapus baris yang kosong itu lebih dulu.');
                    }
                },
            ];

            $rules['golongan_id'] = [
                Rule::requiredIf(fn () => $this->filled('sub_jenis')),
                'nullable',
                Rule::exists('golongans', 'id'),
            ];
        } elseif ($category && $category->golonganBolehDiubah()) {
            // Sudah dipakai aset tapi golongan masih kosong (sisa data lama): boleh dilengkapi
            $rules['golongan_id'] = [
                Rule::requiredIf(fn () => filled($category->sub_jenis)),
                'nullable',
                Rule::exists('golongans', 'id'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'keterangan_fungsi.max' => 'Keterangan Fungsi maksimal 255 karakter.',
            'sub_jenis.digits'      => 'Sub Jenis harus 2 digit angka (contoh: 47).',
            'sub_jenis.unique'      => 'Kombinasi Kode Aktiva Tetap dan Sub Jenis ini sudah ada.',
            'golongan_id.required'  => 'Golongan AT wajib dipilih karena menentukan masa manfaat aset.',
            'golongan_id.exists'    => 'Golongan AT yang dipilih tidak valid.',
        ];
    }
}