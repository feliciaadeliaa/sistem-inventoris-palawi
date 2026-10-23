<?php
// app/Http/Controllers/Admin/ItemController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\ItemsImport;
use App\Exports\ItemImportTemplateExport;
use App\Exports\ItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Illuminate\Support\Arr;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with(['category', 'location'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('item_id', 'like', "%{$search}%")
                        ->orWhere('nomor_aktiva_tetap', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->filled('kondisi'), fn ($q) => $q->where('kondisi', $request->kondisi))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('at_ibat'), fn ($q) => $q->where('at_ibat', $request->at_ibat))
            ->when($request->filled('tahun_dari'), fn ($q) => $q->where('tahun_perolehan', '>=', $request->tahun_dari))
            ->when($request->filled('tahun_sampai'), fn ($q) => $q->where('tahun_perolehan', '<=', $request->tahun_sampai))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Filter memakai semua kategori & lokasi (termasuk yang nonaktif),
        // supaya aset lama tetap bisa difilter.
        $categories = Category::siapDipakai()->urut()->get();
        $locations = Location::urut()->get();
        $golonganOptions = Item::whereNotNull('at_ibat')
            ->where('at_ibat', '!=', '')
            ->distinct()
            ->orderBy('at_ibat')
            ->pluck('at_ibat');

        return view('admin.items.index', compact('items', 'categories', 'locations', 'golonganOptions'));
    }

    public function create()
    {
        // Aset baru hanya boleh memakai kategori dan lokasi yang aktif
        $categories = Category::with('golongan')->siapDipakai()->aktif()->urut()->get();
        $locations = Location::aktif()->urut()->get();

        return view('admin.items.create', compact('categories', 'locations'));
    }

    public function store(StoreItemRequest $request)
    {
        $data = Arr::except($request->validated(), ['masa_manfaat', 'gambar_asset']);

        // Simpan foto (kalau ada) sebelum transaksi DB; path-nya yang disimpan ke kolom.
        if ($request->hasFile('gambar_asset')) {
            $data['gambar_asset'] = $request->file('gambar_asset')->store('assets', 'public');
        }

        try {
            // Nomor urut dan nomor aktiva selalu dibuat server, tidak pernah dari input user.
            DB::transaction(function () use ($data) {
                $data['nomor_urut'] = Item::nextNomorUrut();
                $data['nomor_aktiva_tetap'] = Item::make($data)->buildNomorAktiva();

                Item::create($data);
            });
        } catch (\Throwable $e) {
            // Gagal simpan: jangan tinggalkan file foto yatim
            if (!empty($data['gambar_asset'])) {
                Storage::disk('public')->delete($data['gambar_asset']);
            }
            throw $e;
        }

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        // Form edit terkunci (hanya nama & foto yang bisa diubah), jadi tampilkan SEMUA
        // kategori & lokasi, termasuk yang sudah nonaktif, supaya data aset lama tetap tampil benar.
        $categories = Category::with('golongan')->urut()->get();
        $locations = Location::urut()->get();

        return view('admin.items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $oldFoto = $item->gambar_asset;
        $newFoto = $request->hasFile('gambar_asset')
            ? $request->file('gambar_asset')->store('assets', 'public')
            : null;

        try {
            // Hanya nama barang & foto. Nomor urut / nomor aktiva TIDAK disentuh.
            $item->nama_barang = $request->validated()['nama_barang'];

            if ($newFoto) {
                $item->gambar_asset = $newFoto;
            }

            $item->save();
        } catch (\Throwable $e) {
            if ($newFoto) {
                Storage::disk('public')->delete($newFoto);
            }
            throw $e;
        }

        // Berhasil: hapus foto lama kalau diganti
        if ($newFoto && $oldFoto) {
            Storage::disk('public')->delete($oldFoto);
        }

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        // Aset yang sedang dipinjam tidak boleh dihapus
        if ($item->status === 'dipinjam' || $item->activeLoan()->exists()) {
            return redirect()
                ->route('barang.index')
                ->with('error', 'Barang sedang dipinjam, tidak bisa dihapus. Selesaikan pengembaliannya dulu.');
        }

        // Soft delete: data, foto, dan riwayat tetap tersimpan, bisa dilihat di Trash
        $item->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang dipindahkan ke Trash.');
    }

    public function trash(Request $request)
    {
        $items = Item::onlyTrashed()
            ->with(['category', 'location'])
            ->withCount('transactions')
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('item_id', 'like', "%{$search}%")
                        ->orWhere('nomor_aktiva_tetap', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.items.trash', compact('items'));
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        // Sisa cara hapus lama (status 'nonaktif' + is_active false): kembalikan ke normal
        if ($item->status === 'nonaktif') {
            $item->status = 'tersedia';
        }
        $item->is_active = true;
        $item->save();

        $item->restore();

        return redirect()
            ->route('barang.trash')
            ->with('success', "Barang \"{$item->nama_barang}\" berhasil dipulihkan.");
    }

    public function forceDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);

        // Aset yang punya riwayat transaksi tidak boleh dihapus permanen,
        // supaya riwayat peminjaman/mutasi tidak kehilangan datanya.
        if ($item->transactions()->exists()) {
            return redirect()
                ->route('barang.trash')
                ->with('error', "Barang \"{$item->nama_barang}\" punya riwayat transaksi, tidak bisa dihapus permanen. Biarkan di Trash atau pulihkan.");
        }

        $nama = $item->nama_barang;
        $foto = $item->gambar_asset;

        try {
            $item->forceDelete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Masih dipakai tabel lain (misalnya data perbaikan)
            return redirect()
                ->route('barang.trash')
                ->with('error', "Barang \"{$nama}\" masih terkait data lain, tidak bisa dihapus permanen.");
        }

        if ($foto) {
            Storage::disk('public')->delete($foto);
        }

        return redirect()
            ->route('barang.trash')
            ->with('success', "Barang \"{$nama}\" dihapus permanen.");
    }

    public function showQr(Item $item)
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($item->item_id)
            ->size(300)
            ->build();

        return response($result->getString())
            ->header('Content-Type', 'image/svg+xml');
    }

    public function downloadQr(Item $item, Request $request)
    {
        $format = $request->query('format', 'png');

        return match ($format) {
            'pdf' => $this->downloadQrAsPdf($item),
            default => $this->downloadQrAsPng($item),
        };
    }

    private function downloadQrAsPng(Item $item)
    {
        $pngData = $this->generateLabelPngBinary($item);

        return response($pngData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$item->item_id.'.png"');
    }

    /**
     * Gambar 1 label (border + logo + QR + nomor aktiva + footer) dan kembalikan binary PNG-nya.
     * Layout diselaraskan dengan template PDF: garis tepi rapat ke konten.
     * Dipakai baik untuk download satuan maupun untuk dikumpulkan jadi ZIP massal.
     */
    private function generateLabelPngBinary(Item $item): string
    {
        $qrResult = Builder::create()
            ->writer(new PngWriter())
            ->data($item->item_id)
            ->size(400)
            ->margin(0)
            ->build();

        $qrImage  = imagecreatefromstring($qrResult->getString());
        $qrWidth  = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // Jarak konten ke garis tepi dibuat rapat, senada dengan versi PDF
        $borderPadding  = 0;  // jarak dari tepi kanvas ke garis tepi (border)
        $innerPadding   = 36; // jarak dari garis tepi ke konten (logo/QR/teks)
        $logoHeight     = 60;
        $gapLogoQr      = 10;
        $gapQrNomor     = 24;
        $gapNomorFooter = 20;
        $textBlock      = 60;

        $contentWidth  = $qrWidth;
        $contentHeight = $logoHeight + $gapLogoQr + $qrHeight + $textBlock;

        $canvasWidth  = $contentWidth  + ($innerPadding * 2) + ($borderPadding * 2);
        $canvasHeight = $contentHeight + ($innerPadding * 2) + ($borderPadding * 2);

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        $border = imagecolorallocate($canvas, 51, 51, 51); // #333, senada dengan border PDF
        imagefill($canvas, 0, 0, $white);

        // Gambar garis tepi (border), 1px, mengelilingi seluruh konten
        imagerectangle(
            $canvas,
            $borderPadding,
            $borderPadding,
            $canvasWidth - $borderPadding - 1,
            $canvasHeight - $borderPadding - 1,
            $border
        );

        $contentX = $borderPadding + $innerPadding;
        $currentY = $borderPadding + $innerPadding;

        // Logo
        $logoPath = public_path('images/logo/logo-palawi.png');
        if (file_exists($logoPath)) {
            $logo = imagecreatefrompng($logoPath);
            $logoOrigW = imagesx($logo);
            $logoOrigH = imagesy($logo);
            $newLogoW  = intval($logoHeight * ($logoOrigW / $logoOrigH));
            $logoX     = intval($contentX + ($contentWidth - $newLogoW) / 2);

            imagecopyresampled($canvas, $logo, $logoX, $currentY, 0, 0, $newLogoW, $logoHeight, $logoOrigW, $logoOrigH);
            imagedestroy($logo);
        }
        $currentY += $logoHeight + $gapLogoQr;

        // QR code
        $qrX = $contentX;
        $qrY = $currentY;
        imagecopy($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);
        imagedestroy($qrImage);
        $currentY += $qrHeight;

        // Nomor aset & footer
        $fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $textY = $currentY + $gapQrNomor;

        $this->drawCenteredTextInBox($canvas, $item->nomor_aktiva_tetap ?? '-', $fontPath, 12, $black, $contentX, $contentWidth, $textY);
        $textY += $gapNomorFooter;
        $this->drawCenteredTextInBox($canvas, 'PT Perhutani Alam Wisata Risorsis', $fontPath, 9, $black, $contentX, $contentWidth, $textY);

        ob_start();
        imagepng($canvas);
        $pngData = ob_get_clean();
        imagedestroy($canvas);

        return $pngData;
    }

    private function drawCenteredTextInBox($canvas, $text, $fontPath, $size, $color, $boxX, $boxWidth, $y)
    {
        $bbox = imagettfbbox($size, 0, $fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = intval($boxX + ($boxWidth - $textWidth) / 2);
        imagettftext($canvas, $size, 0, $x, $y, $color, $fontPath, $text);
    }

    private function downloadQrAsPdf(Item $item)
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($item->item_id)
            ->size(300)
            ->build();

        $pdf = Pdf::loadView('barang.qr-label-pdf', [
            'item'   => $item,
            'qrData' => $result->getDataUri(),
        ]);

        return $pdf->download('qr-'.$item->item_id.'.pdf');
    }

    public function printLabels(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
        ]);

        $items = Item::whereIn('id', $request->ids)->get();

        $itemsWithQr = $items->map(function ($item) {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($item->item_id)
                ->size(300)
                ->build();

            return [
                'item' => $item,
                'qrData' => $result->getDataUri(),
            ];
        });

        $pages = $itemsWithQr->chunk(20)->map(fn ($page) => $page->chunk(4));

        $pdf = Pdf::loadView('admin.items.print-labels-pdf', [
            'pages' => $pages,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('label-qr-barang.pdf');
    }

    /**
     * Cetak label sebagai PNG massal, dibungkus jadi 1 file ZIP.
     */
    public function printLabelsPng(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
        ]);

        $items = Item::whereIn('id', $request->ids)->get();

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipFileName = 'label-qr-barang-' . now()->format('YmdHis') . '.zip';
        $zipPath = $tempDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $usedNames = [];

        foreach ($items as $item) {
            $pngData = $this->generateLabelPngBinary($item);

            $baseName = 'qr-' . $item->item_id . '.png';
            $fileName = $baseName;
            $suffix = 1;
            while (in_array($fileName, $usedNames)) {
                $fileName = 'qr-' . $item->item_id . '-' . $suffix . '.png';
                $suffix++;
            }
            $usedNames[] = $fileName;

            $zip->addFromString($fileName, $pngData);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function importForm()
    {
        return view('admin.items.import'); // SESUAIKAN dengan lokasi view kamu
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        Excel::import(new ItemsImport, $request->file('file'));

        return redirect()->route('barang.index')
            ->with('success', 'Data asset berhasil diimport.');
    }

    public function template()
    {
        return Excel::download(new ItemImportTemplateExport, 'template_import_asset.xlsx');
    }

    /**
     * Download data aset ke Excel (tanpa kolom foto).
     * - Kalau ada checkbox yang dipilih (ids[]), hanya itu yang diexport.
     * - Kalau tidak ada, export mengikuti filter yang sedang aktif di halaman index.
     */
    public function export(Request $request)
    {
        $request->validate([
            'ids'   => 'nullable|array',
            'ids.*' => 'exists:items,id',
        ]);

        return Excel::download(
            new ItemsExport(
                $request->input('ids', []),
                $request->only([
                    'q', 'category_id', 'location_id', 'kondisi',
                    'status', 'at_ibat', 'tahun_dari', 'tahun_sampai',
                ])
            ),
            'data-aset-' . now()->format('Ymd-His') . '.xlsx'
        );
    }
}