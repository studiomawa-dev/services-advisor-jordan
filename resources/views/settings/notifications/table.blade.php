@section('css')
@endsection

<div class="table-responsive notifications-table">
    {!! $dataTable->table(['width' => '100%', 'class' => 'table table-bordered']) !!}
</div>

@section('scripts')
    {!! $dataTable->scripts() !!}
@endsection
