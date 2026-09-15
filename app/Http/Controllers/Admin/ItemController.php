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
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class ItemController extends Controller
{
public function index(Request $request)
{
    $items = Item::with(['category', 'location'])
        ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
        ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->location_id))
        ->when($request->filled('kondisi'), fn ($q) => $q->where('kondisi', $request->kondisi))
        ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
        ->when($request->filled('golongan_at'), fn ($q) => $q->where('golongan_at', $request->golongan_at))
        ->when($request->filled('tahun_dari'), fn ($q) => $q->where('tahun_perolehan', '>=', $request->tahun_dari))
        ->when($request->filled('tahun_sampai'), fn ($q) => $q->where('tahun_perolehan', '<=', $request->tahun_sampai))
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $categories = Category::orderBy('nama_kategori')->get();
    $locations = Location::orderBy('nama_lokasi')->get();
    $golonganOptions = Item::whereNotNull('golongan_at')
        ->where('golongan_at', '!=', '')
        ->distinct()
        ->orderBy('golongan_at')
        ->pluck('golongan_at');

    return view('admin.items.index', compact('items', 'categories', 'locations', 'golonganOptions'));
}

    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('admin.items.create', compact('categories', 'locations'));
    }

    public function store(StoreItemRequest $request)
    {
        Item::create($request->validated());

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('admin.items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->update(['is_active' => false, 'status' => 'nonaktif']);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dinonaktifkan.');
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
            'svg' => $this->downloadQrAsSvg($item),
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
     * Gambar 1 label (logo + QR + nomor aktiva + footer) dan kembalikan binary PNG-nya.
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

        $padding    = 20;
        $logoHeight = 60;
        $textBlock  = 60;
        $canvasWidth  = $qrWidth + ($padding * 2);
        $canvasHeight = $padding + $logoHeight + 10 + $qrHeight + $textBlock + $padding;

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        imagefill($canvas, 0, 0, $white);

        $logoPath = public_path('images/logo/logo-palawi.png');
        if (file_exists($logoPath)) {
            $logo = imagecreatefrompng($logoPath);
            $logoOrigW = imagesx($logo);
            $logoOrigH = imagesy($logo);
            $newLogoW  = intval($logoHeight * ($logoOrigW / $logoOrigH));
            $logoX     = intval(($canvasWidth - $newLogoW) / 2);

            imagecopyresampled($canvas, $logo, $logoX, $padding, 0, 0, $newLogoW, $logoHeight, $logoOrigW, $logoOrigH);
            imagedestroy($logo);
        }

        $qrX = $padding;
        $qrY = $padding + $logoHeight + 10;
        imagecopy($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);
        imagedestroy($qrImage);

        $fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $textY = $qrY + $qrHeight + 24;

        $this->drawCenteredText($canvas, $item->nomor_aktiva_tetap ?? '-', $fontPath, 12, $black, $canvasWidth, $textY);
        $textY += 20;
        $this->drawCenteredText($canvas, 'Asset milik PT Perhutani Alam Wisata Risorsis', $fontPath, 9, $black, $canvasWidth, $textY);

        ob_start();
        imagepng($canvas);
        $pngData = ob_get_clean();
        imagedestroy($canvas);

        return $pngData;
    }

    private function drawCenteredText($canvas, string $text, string $fontPath, int $fontSize, int $color, int $canvasWidth, int $y): void
    {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $x = intval(($canvasWidth - $textWidth) / 2);
        imagettftext($canvas, $fontSize, 0, $x, $y, $color, $fontPath, $text);
    }

    private function downloadQrAsSvg(Item $item)
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($item->item_id)
            ->size(500)
            ->build();

        return response($result->getString())
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$item->item_id.'.svg"');
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

    /**
     * Cetak label sebagai 1 file PDF, grid 3x3 (maks 9 label per halaman).
     */
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

        // Bagi jadi grup 9 per halaman, tiap grup dibagi lagi jadi baris isi 3
        $pages = $itemsWithQr->chunk(9)->map(fn ($page) => $page->chunk(3));

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
}