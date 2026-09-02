@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold text-white mb-6">Permintaan perbaikan</h1>

    @if (session('success'))
        <div class="border border-green-700 bg-green-900/30 text-green-300 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($transactions as $transaction)
            <div class="border border-gray-700 rounded-xl p-5 bg-gray-900/40">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-white font-semibold">{{ $transaction->item->nama_barang ?? '—' }}</h2>
                    <span class="text-gray-300 text-xs border border-gray-600 rounded px-2 py-1">
                        {{ ucwords(str_replace('_', ' ', $transaction->status)) }}
                    </span>
                </div>
                <p class="text-gray-400 text-sm mb-1">{{ $transaction->item->item_id ?? '' }}</p>
                <p class="text-gray-400 text-sm mb-3">
                    Diajukan oleh {{ $transaction->user->name ?? '—' }} · {{ $transaction->created_at->format('d M Y H:i') }}
                </p>
                <p class="text-gray-300 text-sm mb-4">{{ $transaction->keterangan }}</p>

                @if ($transaction->status === 'menunggu_approval')
                    <form method="POST" action="{{ route('admin.transaksi.perbaikan.process', $transaction) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-sm font-medium rounded-lg px-4 py-2">
                            Proses (teruskan ke GM)
                        </button>
                    </form>
                @elseif ($transaction->status === 'dalam_perbaikan')
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('admin.transaksi.perbaikan.complete', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="hasil" value="berhasil">
                            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-sm font-medium rounded-lg px-4 py-2">
                                Tandai berhasil
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.transaksi.perbaikan.complete', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="hasil" value="gagal">
                            <button type="submit" class="border border-red-600 text-red-400 text-sm font-medium rounded-lg px-4 py-2 hover:bg-red-900/30">
                                Tandai gagal
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-sm">Belum ada permintaan perbaikan.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>
@endsection