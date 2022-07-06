<div class="table-responsive">
    <table class="table" id="roles-table">
        <thead>
            <tr>
                <th>{{ __('app.Name') }}</th>
                <th>{{ __('app.Display Name') }}</th>
                <th class="action-col">{{ __('app.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{!! $role->name !!}</td>
                <td>{!! $role->display_name !!}</td>
                <td>
                    {!! Form::open(['route' => ['settings.roles.destroy', $role->id], 'method' => 'delete', 'class' => 'text-center']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('settings.roles.show', [$role->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-eye-outline"></i></a>
                        @if(Auth::user()->isAdmin())
                        <a href="{!! route('settings.roles.edit', [$role->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-square-edit-outline"></i></a>
                        {!! Form::button('<i class="mdi mdi-delete-outline"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        @endif
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>