<?php
// app/Http/Controllers/Admin/CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Golongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('golongan')
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('kode_aktiva_tetap', 'like', "%{$search}%")
                        ->orWhere('jenis_aktiva_tetap', 'like', "%{$search}%")
                        ->orWhere('sub_jenis', 'like', "%{$search}%")
                        ->orWhere('keterangan_fungsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode_aktiva_tetap')
            ->orderBy('sub_jenis')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $golongans = Golongan::orderBy('kode')->get();

        return view('admin.categories.create', compact('golongans'));
    }

    public function store(StoreCategoryRequest $request)
    {
        // Sudah termasuk golongan_id (wajib kalau sub_jenis diisi)
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Kalau jenis ini sebelumnya hanya berupa baris tanpa sub jenis,
            // hapus baris kosong itu (kecuali sudah dipakai barang, termasuk yang di Trash).
            if (!empty($data['sub_jenis'])) {
                Category::where('kode_aktiva_tetap', $data['kode_aktiva_tetap'])
                    ->whereNull('sub_jenis')
                    ->whereDoesntHave('items', fn ($q) => $q->withTrashed())
                    ->delete();
            }

            $next = Category::pluck('category_id')
                ->map(fn ($id) => (int) substr($id, 1))
                ->max() + 1;

            Category::create($data + [
                'category_id' => 'C' . str_pad($next, 2, '0', STR_PAD_LEFT),
            ]);
        });

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $golongans = Golongan::orderBy('kode')->get();

        return view('admin.categories.edit', compact('category', 'golongans'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        // Kode, jenis, dan sub jenis membentuk nomor aktiva, jadi tidak pernah ikut diubah.
        // validated() hanya berisi keterangan_fungsi, plus golongan_id kalau golongan masih boleh diubah.
        $category->update($request->validated());

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // withTrashed(): aset di Trash juga masih membutuhkan kategorinya
        if ($category->items()->withTrashed()->exists()) {
            return redirect()
                ->route('kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh barang (termasuk yang ada di Trash).');
        }

        $category->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}