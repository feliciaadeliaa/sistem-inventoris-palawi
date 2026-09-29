@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Tambah Barang') }}
        </h2>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('barang.store') }}" class="space-y-5">
            @csrf
            @include('admin.items._form')
        </form>
    </div>
@endsection