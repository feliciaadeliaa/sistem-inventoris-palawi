<?php
// app/Http/Requests/StoreCategoryRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'   => ['required', 'string', 'max:5', 'unique:categories,category_id'],
            'nama_kategori' => ['required', 'string', 'max:255'],
        ];
    }
}