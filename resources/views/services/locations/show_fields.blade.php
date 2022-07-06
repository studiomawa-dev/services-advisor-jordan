<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', __('app.Id:')) !!}
    <p>{!! $location->id !!}</p>
</div>

<!-- Country Id Field -->
<div class="form-group">
    {!! Form::label('country_id', __('app.Country Id:')) !!}
    <p>{!! $location->country_id !!}</p>
</div>

<!-- City Id Field -->
<div class="form-group">
    {!! Form::label('city_id', __('app.City Id:')) !!}
    <p>{!! $location->city_id !!}</p>
</div>

<!-- District Id Field -->
<div class="form-group">
    {!! Form::label('district_id', __('app.Province Id:')) !!}
    <p>{!! $location->district_id !!}</p>
</div>

<!-- Latitude Field -->
<div class="form-group">
    {!! Form::label('latitude', __('app.Latitude:')) !!}
    <p>{!! $location->latitude !!}</p>
</div>

<!-- Longitude Field -->
<div class="form-group">
    {!! Form::label('longitude', __('app.Longitude:')) !!}
    <p>{!! $location->longitude !!}</p>
</div>