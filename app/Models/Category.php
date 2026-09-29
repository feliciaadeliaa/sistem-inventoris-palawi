<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_id',
        'kode_aktiva_tetap',
        'jenis_aktiva_tetap',
        'sub_jenis',
        'keterangan_fungsi',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'category_id');
    }

    // Segmen ke-3 nomor aktiva (4 digit), contoh: "0947". Null kalau belum punya sub jenis.
    public function getKodeGabunganAttribute(): ?string
    {
        return $this->sub_jenis
            ? $this->kode_aktiva_tetap . $this->sub_jenis
            : null;
    }

    // Label untuk ditampilkan di tabel, dropdown, laporan, dan export.
    // Contoh: "Perlgk Kantor & Kend Tak Bmt - P.ELEK-AC"
    public function getNamaKategoriAttribute(): string
    {
        if (!$this->sub_jenis) {
            return $this->jenis_aktiva_tetap;
        }

        return $this->jenis_aktiva_tetap . ' - ' . ($this->keterangan_fungsi ?: $this->sub_jenis);
    }

    // Hanya baris yang sudah punya sub jenis (dipakai untuk dropdown di form Item dan filter)
    public function scopeSiapDipakai($query)
    {
        return $query->whereNotNull('sub_jenis');
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('kode_aktiva_tetap')->orderBy('sub_jenis');
    }
}