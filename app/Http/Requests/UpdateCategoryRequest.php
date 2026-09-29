<?php
// app/Http/Requests/UpdateCategoryRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Saat edit hanya Keterangan Fungsi yang boleh berubah.
        // Kode, jenis, dan sub jenis dikunci karena membentuk nomor aktiva.
        return [
            'keterangan_fungsi' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan_fungsi.max' => 'Keterangan Fungsi maksimal 255 karakter.',
        ];
    }
}