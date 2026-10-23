@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Edit Barang') }}
        </h2>
        <p class="page-desc">
            {{ __('Hanya nama barang dan foto yang dapat diubah. Field berwarna abu-abu dikunci agar nomor aktiva tetap, mutasi, dan penyusutan tetap konsisten.') }}
        </p>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('barang.update', $item) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('put')
            @include('admin.items._form', ['locked' => true])
        </form>
    </div>
@endsection