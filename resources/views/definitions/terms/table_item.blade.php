@foreach($terms as $term)

<tr class="level level-x-<?= $term['level'] ?><?= $term['level'] > 0 ? ' collapse' : '' ?> collapse-item" data-id="<?= $term['id'] ?>" data-parent="<?= $term['parent_id'] ?>">

    <th>
        @if(isset($term['children']) && count($term['children']) > 0)
        <span class="collapse-icon"></span>
        @endif
        {!! $term['id'] !!}
    </th>
    <td>
        @if($term['color'])
        <span class="service-category-item" style="background-color:{!! $term['color'] !!}; display:block; width: 40px; height: 20px;"></span>
        @endif
    </td>
    <td>
        {!! $term['langs'][0]['name'] !!}
    </td>
    <td>
        {!! Form::open(['route' => ['definitions.terms.destroy', $term['id']], 'method' => 'delete', 'class' => 'text-center']) !!}
        <div class='btn-group'>
            @if(Auth::user()->isAdmin())
            <a href="{!! route('definitions.terms.edit', [$term['id']]) !!}" class='btn btn-default btn-xs'><i class="mdi mdi-square-edit-outline"></i></a>
            {!! Form::button('<i class="mdi mdi-delete-outline"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
            @endif
        </div>
        {!! Form::close() !!}
    </td>
</tr>

@if(isset($term['children']) && count($term['children']) > 0)

@include('definitions.terms.table_item', ['terms' => $term['children']])

@endif

@endforeach