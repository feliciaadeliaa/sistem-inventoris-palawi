<?php
// app/Http/Controllers/Admin/LocationController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Imports\LocationsImport;
use App\Models\Location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q', $request->input('search'));

        $locations = Location::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_wisata', 'like', "%{$search}%")
                        ->orWhere('kode_lokasi', 'like', "%{$search}%")
                        ->orWhere('kode_unit_bisnis', 'like', "%{$search}%")
                        ->orWhere('unit_bisnis', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('unit_bisnis'), fn ($q) => $q->where('unit_bisnis', $request->unit_bisnis))
            ->urut()
            ->paginate(15)
            ->withQueryString();

        $unitBisnisOptions = Location::select('kode_unit_bisnis', 'unit_bisnis')
            ->distinct()
            ->orderBy('kode_unit_bisnis')
            ->pluck('unit_bisnis');

        return view('admin.locations.index', compact('locations', 'unitBisnisOptions'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        Location::create($request->validated());

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        // Kode klaster dan kode lokasi membentuk nomor aktiva, jadi tidak boleh berubah.
        $location->update($request->only('nama_wisata'));

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->items()->exists()) {
            return redirect()
                ->route('lokasi.index')
                ->with('error', 'Lokasi tidak bisa dihapus karena masih dipakai oleh barang.');
        }

        $location->delete();

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }

    public function importForm()
    {
        return view('admin.locations.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new LocationsImport();
        Excel::import($import, $request->file('file'));

        $failureMessages = $import->failures()
            ->map(fn ($failure) => "Baris {$failure->row()}: " . implode(', ', $failure->errors()))
            ->implode(' | ');

        $message = "Berhasil import {$import->imported} data baru. Diperbarui: {$import->updated}.";

        if ($failureMessages) {
            return redirect()
                ->route('lokasi.index')
                ->with('error', $message . " Gagal: {$failureMessages}");
        }

        return redirect()
            ->route('lokasi.index')
            ->with('success', $message);
    }
}