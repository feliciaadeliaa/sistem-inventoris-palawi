<?php
// app/Http/Requests/StoreCategoryRequest.php
namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kode = $this->input('kode_aktiva_tetap');
        $current = $this->route('category')?->category_id; // null saat store

        return [
            'kode_aktiva_tetap' => [
                'bail', 'required', 'digits:2',
                function ($attribute, $value, $fail) use ($current) {
                    // Cegah dua baris "tanpa sub jenis" untuk kode yang sama
                    if ($this->filled('sub_jenis')) {
                        return;
                    }

                    $ada = Category::where('kode_aktiva_tetap', $value)
                        ->whereNull('sub_jenis')
                        ->when($current, fn ($q) => $q->where('category_id', '!=', $current))
                        ->exists();

                    if ($ada) {
                        $fail('Jenis ini sudah terdaftar tanpa sub jenis. Isi Sub Jenis untuk menambah baris baru.');
                    }
                },
            ],

            'jenis_aktiva_tetap' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($kode, $current) {
                    // Satu kode harus selalu punya nama jenis yang sama
                    $beda = Category::where('kode_aktiva_tetap', $kode)
                        ->when($current, fn ($q) => $q->where('category_id', '!=', $current))
                        ->where('jenis_aktiva_tetap', '!=', $value)
                        ->exists();

                    if ($beda) {
                        $fail('Nama jenis untuk kode ini harus sama dengan data yang sudah ada.');
                    }
                },
            ],

            'sub_jenis' => [
                'nullable', 'digits:2',
                Rule::unique('categories', 'sub_jenis')
                    ->where('kode_aktiva_tetap', $kode)
                    ->ignore($current, 'category_id'),
            ],

            'keterangan_fungsi' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_aktiva_tetap.digits' => 'Kode Aktiva Tetap harus 2 digit angka (contoh: 09).',
            'sub_jenis.digits'         => 'Sub Jenis harus 2 digit angka (contoh: 47).',
            'sub_jenis.unique'         => 'Kombinasi Kode Aktiva Tetap dan Sub Jenis ini sudah ada.',
        ];
    }
}