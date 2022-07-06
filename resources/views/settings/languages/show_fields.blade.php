<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $language->id !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $language->name !!}</p>
</div>

<!-- Code Field -->
<div class="form-group">
    {!! Form::label('code', 'Code:') !!}
    <p>{!! $language->code !!}</p>
</div>

<!-- Is Default Field -->
<div class="form-group">
    {!! Form::label('is_default', 'Is Default:') !!}
    <p>{!! $language->is_default !!}</p>
</div>

<!-- Is Rtl Field -->
<div class="form-group">
    {!! Form::label('is_rtl', 'Is Rtl:') !!}
    <p>{!! $language->is_rtl !!}</p>
</div>

<!-- Fb Lang Code Field -->
<div class="form-group">
    {!! Form::label('fb_lang_code', 'Fb Lang Code:') !!}
    <p>{!! $language->fb_lang_code !!}</p>
</div>

