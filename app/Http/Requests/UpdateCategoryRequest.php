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
        return [
            // category_id tidak diubah saat edit (jadi tidak divalidasi unique di sini)
            'nama_kategori' => ['required', 'string', 'max:255'],
        ];
    }
}