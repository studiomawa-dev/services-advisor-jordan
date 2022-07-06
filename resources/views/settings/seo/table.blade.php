<div class="table-responsive">
    <table class="table" id="roles-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th class="action-col">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($seo_configs as $seo_config)
            <tr>
                <td>{!! $seo_config->title !!}</td>
                <td>{!! $seo_config->value !!}</td>
                <td>
                    <div class='btn-group'>
						@if(Auth::user()->isAdmin())
                        <a href="{!! route('settings.roles.edit', [$seo_config->id]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-square-edit-outline"></i></a>
						@endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
