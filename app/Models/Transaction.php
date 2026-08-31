<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
    'item_id',
    'user_id',
    'jenis_transaksi',
    'status',
    'keterangan',
    'approved_by',
    'approved_at',
    'tanggal_kembali_estimasi',
    'tanggal_kembali_aktual',
    'lokasi_asal_id',
    'lokasi_tujuan_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'tanggal_kembali_estimasi' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    // ==== Relasi ====

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user() // pengaju
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lokasiAsal()
{
    return $this->belongsTo(Location::class, 'lokasi_asal_id');
}

public function lokasiTujuan()
{
    return $this->belongsTo(Location::class, 'lokasi_tujuan_id');
}

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_transaksi', $jenis);
    }

    public function scopeStockOut($query)
    {
        return $query->where('jenis_transaksi', 'stock_out');
    }

    public function scopeMenungguApproval($query)
    {
        return $query->where('status', 'menunggu_approval');
    }

    public function scopeMilikUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}