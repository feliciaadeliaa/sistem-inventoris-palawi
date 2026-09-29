<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Saat edit hanya Nama Wisata yang boleh berubah.
        return [
            'nama_wisata' => ['required', 'string', 'max:255'],
        ];
    }
}