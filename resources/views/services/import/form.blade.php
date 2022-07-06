@if ($errors->any())
<div class="alert alert-danger">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<form method="post" enctype="multipart/form-data" action="{{ url('/admin/services/import/preview') }}" onsubmit="$('#uploadButton').prop('disabled', true).val('Loading...');">
	{{ csrf_field() }}
	<div class="form-group">
		<table class="table">
			<tr>
				<td width="40%" align="right"><label>{{ __('app.Select Services Template File for Upload') }}</label></td>
				<td width="30">
					<input type="file" name="select_file" />
				</td>
				<td width="30%" align="left">
					<input type="submit" name="upload" id="uploadButton" class="btn btn-primary" value="Upload">
				</td>
			</tr>
			<tr>
				<td width="40%" align="right"></td>
				<td width="30"><span class="text-muted">{{ __('app.Valid File Formats') }}: .xls, .xslx</span></td>
				<td width="30%" align="left"></td>
			</tr>
		</table>
	</div>
</form>