<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'  => ['required', 'string', 'max:255'],
            'gambar_asset' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_barang.required'  => 'Nama barang wajib diisi.',
            'gambar_asset.image'    => 'File harus berupa gambar.',
            'gambar_asset.mimes'    => 'Format foto harus JPG, PNG, atau WEBP.',
            'gambar_asset.max'      => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}