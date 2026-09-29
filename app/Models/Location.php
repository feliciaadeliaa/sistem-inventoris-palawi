<?php
// app/Models/Location.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'kode_unit_bisnis',
        'unit_bisnis',
        'kode_lokasi',
        'nama_wisata',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Segmen ke-4 nomor aktiva (3 digit), contoh: "101" = Klaster Malang + Coban Rondo
    public function getKodeGabunganAttribute(): string
    {
        return $this->kode_unit_bisnis . $this->kode_lokasi;
    }

    // Supaya kode lama yang membaca $location->nama_lokasi tetap jalan
    public function getNamaLokasiAttribute(): string
    {
        return $this->nama_wisata;
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('kode_unit_bisnis')->orderBy('kode_lokasi');
    }
}