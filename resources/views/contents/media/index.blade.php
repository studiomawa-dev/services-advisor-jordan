@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card index-card">
                <div class="card-header">
                    <h4 class="card-title my-1 float-left">Media</h4>
                    <a class="btn btn-primary float-right" href="{!! route('contents.media.create') !!}">Add New</a>
                </div>
                <div class="card-body px-0 py-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="content">
                                <div class="clearfix"></div>

                                @include('flash::message')

                                <div class="clearfix"></div>
                                <div class="box box-primary">
                                    <div class="box-body">
                                            @include('contents.media.table')
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
