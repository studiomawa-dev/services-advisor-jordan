<div class="table-responsive">
    <table class="table" id="terms-table">
        <thead>
            <tr>
                <th width="10%">{{ __('app.Code') }}</th>
                <th width="10%"></th>
                <th width="70%">{{ __('app.Name') }}</th>
                <th class="action-col">{{ __('app.Actions') }}</th>
            </tr>
        </thead>
        <tbody>

            @if(count($terms) > 0)

            @include('definitions.terms.table_item', $terms)

            @else

            <tr>
                <td colspan="3" class="text-center">{{ __('app.No items found') }}.</td>
            </tr>

            @endif
        </tbody>
    </table>
</div>