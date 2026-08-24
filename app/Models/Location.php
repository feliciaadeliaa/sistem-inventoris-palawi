<?php
// app/Models/Location.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['nama_lokasi'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}