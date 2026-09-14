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

class ItemController extends Controller
{
  public function index(Request $request)
    {
    $items = Item::with(['category', 'location'])
        ->when($request->filled('category_id'), function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })
        ->latest()
        ->paginate(15);

    $categories = Category::orderBy('nama_kategori')->get();

    return view('admin.items.index', compact('items', 'categories'));
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
        // Soft-status, bukan hapus (sesuai Modul 2)
        $item->update(['is_active' => false, 'status' => 'nonaktif']);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dinonaktifkan.');
    }

    public function showQr(Item $item)
    {
        return response(
            QrCode::format('svg')->size(300)->generate($item->item_id)
        )->header('Content-Type', 'image/svg+xml');
    }

    public function downloadQr(Item $item, Request $request)
    {
        $format = $request->query('format', 'png'); // default png

        return match ($format) {
            'pdf' => $this->downloadQrAsPdf($item),
            'svg' => $this->downloadQrAsSvg($item),
            default => $this->downloadQrAsPng($item),
        };
    }

    private function downloadQrAsPng(Item $item)
    {
        // 1. Generate QR code sebagai gambar
        $qrResult = Builder::create()
            ->writer(new PngWriter())
            ->data($item->item_id)
            ->size(400)
            ->margin(0)
            ->build();

        $qrImage  = imagecreatefromstring($qrResult->getString());
        $qrWidth  = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // 2. Siapkan canvas kosong
        $padding    = 20;
        $logoHeight = 60;
        $textBlock  = 60; // ruang untuk 3 baris teks di bawah QR
        $canvasWidth  = $qrWidth + ($padding * 2);
        $canvasHeight = $padding + $logoHeight + 10 + $qrHeight + $textBlock + $padding;

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        imagefill($canvas, 0, 0, $white);

        // 3. Tempel logo PT Palawi (di-resize proporsional, center)
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

        // 4. Tempel QR (center, di bawah logo)
        $qrX = $padding;
        $qrY = $padding + $logoHeight + 10;
        imagecopy($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);
        imagedestroy($qrImage);

        // 5. Tulis teks: nama asset, nomor asset tetap, footer
        $fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $textY = $qrY + $qrHeight + 24;

        $this->drawCenteredText($canvas, $item->nomor_asset_tetap ?? '-', $fontPath, 12, $black, $canvasWidth, $textY);
        $textY += 20;
        $this->drawCenteredText($canvas, 'Asset milik PT Perhutani Alam Wisata Risorsis', $fontPath, 9, $black, $canvasWidth, $textY);

        // 6. Output sebagai PNG
        ob_start();
        imagepng($canvas);
        $pngData = ob_get_clean();
        imagedestroy($canvas);

        return response($pngData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$item->item_id.'.png"');
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
            'qrData' => $result->getDataUri(), // base64 data-uri, langsung dipakai di <img src="">
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

        return view('admin.items.print-labels', compact('items'));
    }
}