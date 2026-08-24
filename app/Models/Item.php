<?php
// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_id', 'nama_barang', 'category_id', 'location_id',
        'golongan_at', 'tahun_perolehan', 'masa_manfaat', 'nilai_perolehan',
        'kondisi', 'tanggal_terima', 'status', 'is_active',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'nilai_perolehan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    protected static function booted()
    {
        static::created(function ($item) {
            // Generate item_id setelah record tersimpan, contoh format: ITM-000001
            $item->update(['item_id' => 'ITM-' . str_pad($item->id, 6, '0', STR_PAD_LEFT)]);
        });
    }
}