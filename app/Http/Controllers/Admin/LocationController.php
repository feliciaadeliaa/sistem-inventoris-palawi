<?php
// app/Http/Controllers/Admin/LocationController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Imports\LocationsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class LocationController extends Controller
{
    public function index(Request $request)
{
    $locations = Location::query()
        ->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('nama_lokasi', 'like', "%{$request->search}%")
                    ->orWhere('kode_lokasi', 'like', "%{$request->search}%");
            });
        })
        ->when($request->filled('wilayah'), fn ($q) => $q->where('wilayah', $request->wilayah))
        ->when($request->filled('unit_bisnis'), fn ($q) => $q->where('unit_bisnis', $request->unit_bisnis))
        ->when($request->filled('sub_unit_bisnis'), fn ($q) => $q->where('sub_unit_bisnis', $request->sub_unit_bisnis))
        ->orderBy('nama_lokasi')
        ->paginate(15)
        ->withQueryString();

    $wilayahOptions = Location::whereNotNull('wilayah')->where('wilayah', '!=', '')->distinct()->orderBy('wilayah')->pluck('wilayah');
    $unitBisnisOptions = Location::whereNotNull('unit_bisnis')->where('unit_bisnis', '!=', '')->distinct()->orderBy('unit_bisnis')->pluck('unit_bisnis');
    $subUnitBisnisOptions = Location::whereNotNull('sub_unit_bisnis')->where('sub_unit_bisnis', '!=', '')->distinct()->orderBy('sub_unit_bisnis')->pluck('sub_unit_bisnis');

    return view('admin.locations.index', compact('locations', 'wilayahOptions', 'unitBisnisOptions', 'subUnitBisnisOptions'));
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
        $location->update($request->validated());

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

    $failures = $import->failures();
    $failureMessages = $failures->map(function ($failure) {
        return "Baris {$failure->row()}: " . implode(', ', $failure->errors());
    })->implode(' | ');

    $message = "Berhasil import {$import->imported} data baru. Diperbarui: {$import->updated}.";;

    if ($failureMessages) {
        $message .= " Gagal: {$failureMessages}";
    }

    return redirect()
        ->route('lokasi.index')
        ->with('success', $message);
}
}