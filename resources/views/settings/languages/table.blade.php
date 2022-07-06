<div class="table-responsive">
    <table class="table" id="languages-table">
        <thead>
            <tr>
                <th>{{ __('app.Name') }}</th>
                <th>{{ __('app.Code') }}</th>
                <th>{{ __('app.Is Default') }}</th>
                <th>{{ __('app.Use On Backend') }}</th>
                <th class="action-col">{{ __('app.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $language)
            <tr>
                <td>{!! $language->name !!}</td>
                <td>{!! $language->code !!}</td>
                <td>{!! $language->is_default ? __('app.Yes') : __('app.No') !!}</td>
                <td>{!! $language->is_backend ? __('app.Yes') : __('app.No') !!}</td>
                <td>
                    {!! Form::open(['route' => ['settings.languages.destroy', $language->id], 'method' => 'delete', 'class' => 'text-center']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('settings.languages.show', [$language->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-eye-outline"></i></a>
                        @if(Auth::user()->isAdmin())
                        <a href="{!! route('settings.languages.edit', [$language->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-square-edit-outline"></i></a>
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