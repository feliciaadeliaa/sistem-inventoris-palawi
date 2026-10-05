@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title">Edit Golongan AT</h2>
        <p class="page-desc">
            Perubahan masa manfaat berlaku untuk aset yang disimpan setelahnya. Aset yang sudah tersimpan tidak berubah sampai diedit.
        </p>
    </div>

    <div class="card card-pad">
        <form action="{{ route('golongan.update', $golongan) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.golongan._form', ['golongan' => $golongan])
        </form>
    </div>
@endsection