<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['category_id', 'nama_kategori'];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'category_id');
    }
}