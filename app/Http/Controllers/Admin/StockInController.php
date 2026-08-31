<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class StockInController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])
            ->where('jenis_transaksi', 'Stock In')
            ->latest()
            ->paginate(15);

        return view('admin.stock-in.index', compact('transactions'));
    }
}