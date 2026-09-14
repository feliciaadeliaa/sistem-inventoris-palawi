<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events()
    {
        $user = Auth::user();

        $events = $user->role === 'admin'
            ? $this->buildAdminEvents()
            : $this->buildPersonalEvents($user);

        return response()->json($events);
    }

    private function buildAdminEvents(): array
    {
        $events = [];

        $transactions = Transaction::with(['item', 'user'])->get();

        foreach ($transactions as $trx) {
            $nama = $trx->item->nama_barang ?? '-';

            $events[] = [
                'title' => "{$trx->jenis_transaksi} diajukan - {$nama} ({$trx->user->name})",
                'start' => $trx->created_at->toDateString(),
                'color' => '#f59e0b',
            ];

            if ($trx->approved_at) {
                $events[] = [
                    'title' => "{$trx->jenis_transaksi} diproses Admin - {$nama}",
                    'start' => $trx->approved_at->toDateString(),
                    'color' => '#3b82f6',
                ];
            }

            if ($trx->gm_approved_at) {
                $label = $trx->status === 'ditolak' ? 'ditolak GM' : 'disetujui GM';
                $events[] = [
                    'title' => "{$trx->jenis_transaksi} {$label} - {$nama}",
                    'start' => $trx->gm_approved_at->toDateString(),
                    'color' => $trx->status === 'ditolak' ? '#ef4444' : '#22c55e',
                ];
            }

            if ($trx->tanggal_kembali_aktual) {
                $events[] = [
                    'title' => "Barang dikembalikan - {$nama}",
                    'start' => $trx->tanggal_kembali_aktual->toDateString(),
                    'color' => '#22c55e',
                ];
            }
        }

        return $events;
    }

    private function buildPersonalEvents($user): array
    {
        $events = [];

        // Pengajuan milik sendiri, cuma yang sudah final
        $own = Transaction::with('item')->where('user_id', $user->id)->get();

        foreach ($own as $trx) {
            $nama = $trx->item->nama_barang ?? '-';

            if ($trx->gm_approved_at) {
                $label = $trx->status === 'ditolak' ? 'ditolak' : 'disetujui';
                $events[] = [
                    'title' => "{$trx->jenis_transaksi} {$label} - {$nama}",
                    'start' => $trx->gm_approved_at->toDateString(),
                    'color' => $trx->status === 'ditolak' ? '#ef4444' : '#22c55e',
                ];
            }

            if ($trx->tanggal_kembali_aktual) {
                $events[] = [
                    'title' => "Barang dikembalikan - {$nama}",
                    'start' => $trx->tanggal_kembali_aktual->toDateString(),
                    'color' => '#22c55e',
                ];
            }
        }

        // Khusus GM/Senior Analis: tanggal mereka sendiri melakukan approval
        if (in_array($user->role, ['gm', 'senior_analis'])) {
            $approved = Transaction::with(['item', 'user'])
                ->where('gm_approved_by', $user->id)
                ->get();

            foreach ($approved as $trx) {
                $nama = $trx->item->nama_barang ?? '-';
                $verb = $trx->status === 'ditolak' ? 'menolak' : 'menyetujui';

                $events[] = [
                    'title' => "Anda {$verb} {$trx->jenis_transaksi} - {$nama} ({$trx->user->name})",
                    'start' => $trx->gm_approved_at->toDateString(),
                    'color' => '#8b5cf6',
                ];
            }
        }

        return $events;
    }
}