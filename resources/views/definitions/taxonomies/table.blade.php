<div class="table-responsive">
    <table class="table" id="taxonomies-table">
        <thead>
            <tr>
                <th>{{ __('app.Key') }}</th>
                <th>{{ __('app.Order') }}</th>
                <th>{{ __('app.Deleted') }}</th>
                <th class="action-col">{{ __('app.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxonomies as $taxonomy)
            <tr>
                <td>{!! $taxonomy->key !!}</td>
                <td>{!! $taxonomy->order !!}</td>
                <td>{!! $taxonomy->deleted !!}</td>
                <td>
                    {!! Form::open(['route' => ['definitions.taxonomies.destroy', $taxonomy->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('definitions.taxonomies.show', [$taxonomy->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-eye-outline"></i></a>
                        <a href="{!! route('definitions.taxonomies.edit', [$taxonomy->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-square-edit-outline"></i></a>
                        {!! Form::button('<i class="mdi mdi-delete-outline"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>