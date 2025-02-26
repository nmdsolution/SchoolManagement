@extends('layout.master')

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <h3 class="text-2xl font-bold mb-6">Créer une nouvelle compétence</h3>

        {!! form($form) !!}

        @if ($errors->has('duplicate'))
            <div class="alert alert-danger">
                {{ $errors->first('duplicate') }}
            </div>
        @endif
            </div>
        </div>
    </div>
</div>
@endsection 