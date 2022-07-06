@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title my-1">{{ __('app.Term Detail') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="row" style="padding-left: 20px">
                                        @include('definitions.terms.show_fields')
                                        <a href="{!! route('definitions.terms.index', ['taxonomy' => $term->taxonomy_id]) !!}" class="btn btn-default">{{ __('app.Back To Previous Screen') }}</a>
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