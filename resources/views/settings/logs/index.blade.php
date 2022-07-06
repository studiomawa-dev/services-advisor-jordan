@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card index-card">
            <div class="card-header">
                <h4 class="card-title my-1 float-left">{{ __('app.Logs') }}</h4>
            </div>
            <div class="card-body px-0 py-0">
                <div class="row">
                    <div class="col-12">
                        <form id="filter-form" action="" class="px-1 py-1 table-filter-form">
                            <div class="form-row">
                                <div class="col-lg-10 col-md-6 col-sm-12 mb-2 mb-lg-0">
                                    {{ Form::text('src', $src, ['class' => 'form-control', 'placeholder' => __('app.User, Category, Type or Message')]) }}
                                </div>
                                <div class="col-6 col-lg-1 col-md-3 col-sm-6">
                                    <button class="btn btn-primary btn-block" type="submit"><i class="mdi mdi-filter"></i></button>
                                </div>
                                <div class="col-6 col-lg-1 col-md-3 col-sm-6">
                                    <button class="btn btn-outline-primary btn-block" type="button" onclick="resetFilter()" data-toggle="tooltip" title="{{ __('app.Clear Filter') }}"><i class="mdi mdi-filter-remove"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <div class="clearfix"></div>

                            @include('flash::message')

                            <div class="clearfix"></div>
                            <div class="box box-primary">
                                <div class="box-body">
                                    @include('settings.logs.table')
                                </div>
                            </div>
                            <div class="text-center">

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
    function resetFilter() {
        $('input[name=src]').val('');
        $('#filter-form').submit();
    }
</script>
@endsection