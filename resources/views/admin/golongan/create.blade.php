@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="page-title">Tambah Golongan AT</h2>
        <p class="page-desc">Golongan menentukan masa manfaat aset berdasarkan sub jenisnya.</p>
    </div>

    <div class="card card-pad">
        <form action="{{ route('golongan.store') }}" method="POST">
            @csrf
            @include('admin.golongan._form')
        </form>
    </div>
@endsection