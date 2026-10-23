<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GolonganRequest;
use App\Models\Golongan;
use Illuminate\Http\Request;

class GolonganController extends Controller
{
    public function index(Request $request)
    {
        // q dikirim oleh search bar di header
        $search = trim((string) $request->input('q', ''));

        $golongans = Golongan::withCount('categories')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%")
                      ->orWhere('masa_manfaat', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode')
            ->get();

        return view('admin.golongan.index', compact('golongans', 'search'));
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

    public function toggleActive(Golongan $golongan)
    {
        $golongan->update(['is_active' => !$golongan->is_active]);

        $status = $golongan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('golongan.index')
            ->with('success', "{$golongan->nama} berhasil {$status}.");
    }
}