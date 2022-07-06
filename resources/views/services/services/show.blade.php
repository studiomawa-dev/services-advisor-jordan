@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title my-1">{{ __('app.Service Detail') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="row" style="padding-left: 20px">
                                        {!! Form::model($service, ['route' => ['services.services.index', $service->id]]) !!}
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

@section('additionalscripts')
<script>
    $(document).ready(function() {
        $('#main-accordion').closest("form").find('input,textarea').attr('readonly', 'readonly');
        $('#main-accordion').closest("form").find('select,input[type="checkbox"],button,#start_date,#end_date').attr('disabled', 'disabled');
        $('.footer-buttons,#category-select,#add-category-btn,#selected-category-container .selected-category-item .remove').hide();
        $('#selected-category-container').removeClass('mt-4');
        $('#main-accordion').closest("form").submit(function(event) {
            event.preventDefault();
            return false;
        })

    });
</script>
@endsection