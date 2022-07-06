@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title my-1">{{ __('app.Add New Service') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">

                        <div class="content">
                            @include('common.errors')
                            <div class="box box-primary">

                                <div class="box-body">
                                    <div class="row">
                                        {!! Form::open(['route' => 'services.services.store']) !!}

                                        @include('services.services.fields')

                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection