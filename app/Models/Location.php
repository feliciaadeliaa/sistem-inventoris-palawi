<?php
// app/Models/Location.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nama_lokasi', 'kode_lokasi', 'wilayah', 'unit_bisnis', 'sub_unit_bisnis'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}