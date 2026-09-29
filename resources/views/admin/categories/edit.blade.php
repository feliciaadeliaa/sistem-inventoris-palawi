@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Edit Kategori') }}
        </h2>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('kategori.update', $category) }}" class="space-y-5">
            @csrf
            @method('put')
            @include('admin.categories._form')
        </form>
    </div>
@endsection