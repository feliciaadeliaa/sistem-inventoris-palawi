@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Edit Barang') }}
        </h2>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('barang.update', $item) }}" class="space-y-5">
            @csrf
            @method('put')
            @include('admin.items._form')
        </form>
    </div>
@endsection