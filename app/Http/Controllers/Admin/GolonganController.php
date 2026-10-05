<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GolonganRequest;
use App\Models\Golongan;

class GolonganController extends Controller
{
    public function index()
    {
        $golongans = Golongan::withCount('categories')->orderBy('kode')->get();

        return view('admin.golongan.index', compact('golongans'));
    }

    public function create()
    {
        return view('admin.golongan.create');
    }

    public function store(GolonganRequest $request)
    {
        Golongan::create($request->validated());

        return redirect()->route('golongan.index')->with('success', 'Golongan AT berhasil ditambahkan.');
    }

    public function edit(Golongan $golongan)
    {
        return view('admin.golongan.edit', compact('golongan'));
    }

    public function update(GolonganRequest $request, Golongan $golongan)
    {
        $golongan->update($request->validated());

        return redirect()->route('golongan.index')->with('success', 'Golongan AT berhasil diperbarui.');
    }

    public function destroy(Golongan $golongan)
    {
        if ($golongan->categories()->exists()) {
            return redirect()->route('golongan.index')
                ->with('error', "{$golongan->nama} masih dipakai oleh sub jenis di menu Kategori, tidak bisa dihapus.");
        }

        $golongan->delete();

        return redirect()->route('golongan.index')->with('success', 'Golongan AT berhasil dihapus.');
    }
}