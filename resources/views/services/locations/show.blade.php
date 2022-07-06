@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title my-1">{{ __('app.Location') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="row" style="padding-left: 20px">
                                        {!! Form::model($location, ['route' => ['services.locations.update', $location->id], 'id' => 'location-form', 'disabled' => 'disabled']) !!}

                                        @include('services.locations.fields')

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


@section('pagescripts')
<script>
    $(document).ready(function() {
        $('#location-form').find('input,textarea').attr('readonly', 'readonly');
        $('#location-form').find('select,input[type="checkbox"],button').attr('disabled', 'disabled');
        $('.footer-buttons').hide();
        $('#location-form').submit(function(event) {
            event.preventDefault();
            return false;
        })
    });
</script>
@endsection