<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GolonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('golongan')?->id;

        return [
            'kode'         => ['required', 'regex:/^\d{2}$/', Rule::unique('golongans', 'kode')->ignore($id)],
            'nama'         => ['required', 'string', 'max:50'],
            'masa_manfaat' => ['required', 'integer', 'min:1', 'max:50'],
            'persen'       => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required'         => 'Kode wajib diisi.',
            'kode.regex'            => 'Kode harus 2 digit angka (contoh: 01).',
            'kode.unique'           => 'Kode sudah dipakai golongan lain.',
            'nama.required'         => 'Nama golongan wajib diisi.',
            'masa_manfaat.required' => 'Masa manfaat wajib diisi.',
            'masa_manfaat.integer'  => 'Masa manfaat harus berupa angka bulat (tahun).',
            'persen.required'       => 'Persen wajib diisi.',
            'persen.numeric'        => 'Persen harus berupa angka.',
        ];
    }
}