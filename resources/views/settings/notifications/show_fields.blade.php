<!-- Id Field -->
<div class="form-group col-sm-4">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $notification->id !!}</p>
</div>

<!-- Name Field -->
<div class="form-group col-sm-4">
    {!! Form::label('name', 'Sending Date:') !!}
    <p>{!! $notification->sending_date !!}</p>
</div>

<!-- Email Field -->
<div class="form-group col-sm-4">
    {!! Form::label('email', 'Is Sent:') !!}
    <p>{!! $notification->is_sent !!}</p>
</div>


<div class="col-sm-12 mt-3">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			@foreach($langs as $lang)
			<a class="nav-item nav-link {{ $loop->index == 0 ? 'active' : '' }}" id="lang-{{ $lang->id }}-tab" data-toggle="tab" href="#lang-{{ $lang->id }}" role="tab" aria-controls="lang-{{ $lang->id }}"" aria-selected=" false">{{ $lang->name }}</a>
			@endforeach
		</div>
	</nav>
	<div class="tab-content">
		@foreach($langs as $lang)
		<div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}" role="tabpanel" aria-labelledby="lang-{{ $lang->id }}-tab">
			<div class="row mt-3">
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][title]', 'Title:') !!}
					<p>{!! $notification->langs['l'.$lang->id]->title !!}</p>
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][message]', 'Message:') !!}
					<p>{!! $notification->langs['l'.$lang->id]->message !!}</p>
				</div>

				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][message]', 'Report Name:') !!}
					<p>{!! $notification->langs['l'.$lang->id]->report_name !!}</p>
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>
