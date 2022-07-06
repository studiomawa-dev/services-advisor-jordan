@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title my-1">Page</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="content">
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <div class="row" style="padding-left: 20px">
                                            @include('contents.pages.show_fields')
                                            <a href="{!! route('contents.pages.index') !!}" class="btn btn-default">Back</a>
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
