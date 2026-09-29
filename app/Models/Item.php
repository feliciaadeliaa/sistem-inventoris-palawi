<?php
// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_id', 'nama_barang', 'category_id', 'location_id',
        'nomor_urut', 'nomor_aktiva_tetap',
        'golongan_at', 'tahun_perolehan', 'masa_manfaat', 'nilai_perolehan',
        'kondisi', 'tanggal_terima', 'status', 'is_active',
    ];

    public const KONDISI_LABELS = [
        'B'   => 'Baik',
        'BPR' => 'Butuh Perawatan',
        'RB'  => 'Rusak Berat',
        'RSS' => 'Rusak Sama Sekali',
    ];

    public const STATUS_LABELS = [
        'tersedia'        => 'Tersedia',
        'dipinjam'        => 'Dipinjam',
        'dalam_perbaikan' => 'Dalam Perbaikan',
        'nonaktif'        => 'Nonaktif',
    ];

    // Golongan AT -> digit ke-2 nomor aktiva (I = AT = 1, II = IBAT = 2)
    public const GOLONGAN_KODE = [
        'I'  => 1,
        'II' => 2,
    ];

    public const GOLONGAN_LABELS = [
        'I'  => 'I - AT',
        'II' => 'II - IBAT',
    ];

    protected $casts = [
        'tanggal_terima'  => 'date',
        'nilai_perolehan' => 'decimal:2',
        'is_active'       => 'boolean',
        'nomor_urut'      => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function booted()
    {
        static::created(function ($item) {
            // Generate item_id setelah record tersimpan, contoh format: ITM-000001
            $item->update(['item_id' => 'ITM-' . str_pad($item->id, 6, '0', STR_PAD_LEFT)]);
        });
    }

    /**
     * Nomor urut berikutnya (4 digit pertama nomor aktiva).
     * Harus dipanggil di dalam DB::transaction supaya lockForUpdate efektif.
     * Kalau nanti Item memakai SoftDeletes dan nomor bekas tidak boleh dipakai ulang,
     * ganti jadi: static::withTrashed()->lockForUpdate()->max('nomor_urut')
     */
    public static function nextNomorUrut(): int
    {
        return ((int) static::lockForUpdate()->max('nomor_urut')) + 1;
    }

    /**
     * Susun nomor aktiva 16 digit: URUT.TIPE.KATEGORI.LOKASI.TAHUN
     * Contoh: 0001.1.0947.101.2026
     */
    public function buildNomorAktiva(): string
    {
        $this->unsetRelations()->loadMissing(['category', 'location']);

        $tipe = self::GOLONGAN_KODE[$this->golongan_at]
            ?? throw new \DomainException("Golongan AT '{$this->golongan_at}' tidak valid, harus I atau II.");

        return implode('.', [
            str_pad((string) $this->nomor_urut, 4, '0', STR_PAD_LEFT),
            $tipe,
            $this->category->kode_aktiva_tetap . $this->category->sub_jenis,
            $this->location->kode_gabungan,
            $this->tahun_perolehan,
        ]);
    }
}