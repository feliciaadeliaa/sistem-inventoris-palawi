@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Tambah Kategori') }}
        </h2>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('kategori.store') }}" class="space-y-5">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
@endsection