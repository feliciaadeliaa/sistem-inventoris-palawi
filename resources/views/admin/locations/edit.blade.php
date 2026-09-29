@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Edit Lokasi') }}
        </h2>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('lokasi.update', $location) }}" class="space-y-5">
            @csrf
            @method('put')
            @include('admin.locations._form')
        </form>
    </div>
@endsection