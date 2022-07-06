@if (in_array($name, ['city', 'district', 'partner_id']))
<select class="cell-update-input {!! $name !!} js-select" style="display: none; width:100%; min-width: 300px" name="{!! $name !!}[{!! $index !!}]"></select>
@elseif (in_array($name, ['categories', 'accessibility', 'legal_documents_required', 'nationality',
'gender_age', 'intake_criteria', 'coverage', 'referral_method', 'immediate_next_step_after_referral',
'response_delay_after_referral', 'feedback_mechanism', 'feedback_delay', 'complaints_mechanism']))
<select class="cell-update-input {!! $name !!} js-select" multiple="multiple" style="display: none; width:100%; min-width: 300px" name="terms[{!! $index !!}]"></select>
@else
<input type="text" class="cell-update-input" style="display: none; width:100%; min-width: 300px" name="{!! $name !!}[{!! $index !!}]" value="{!! $value !!}">
@endif