<?php
// app/Http/Controllers/Admin/CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
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
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Kalau jenis ini sebelumnya hanya berupa baris tanpa sub jenis,
            // hapus baris kosong itu (kecuali sudah dipakai barang).
            if (!empty($data['sub_jenis'])) {
                Category::where('kode_aktiva_tetap', $data['kode_aktiva_tetap'])
                    ->whereNull('sub_jenis')
                    ->doesntHave('items')
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
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        // Kode, jenis, dan sub jenis membentuk nomor aktiva, jadi tidak boleh berubah.
        $category->update($request->only('keterangan_fungsi'));

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->exists()) {
            return redirect()
                ->route('kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh barang.');
        }

        $category->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}