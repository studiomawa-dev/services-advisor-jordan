@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title my-1">{{ __('app.Language') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            @include('common.errors')
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="row">
                                        {!! Form::model($language, ['route' => ['settings.languages.update', $language->id], 'method' => 'patch']) !!}

                                        @include('settings.languages.fields')

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