@section('css')
@endsection

<div class="table-responsive">
    {!! $dataTable->table(['width' => '100%', 'class' => 'services-table table table-bordered']) !!}
</div>

@section('scripts')
{!! $dataTable->scripts() !!}
@endsection
