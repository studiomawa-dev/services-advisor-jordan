@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card index-card">
			<div class="card-header">
				<h4 class="card-title my-1 float-left">{{ __('app.Services Import') }}</h4>

			</div>
			<div class="card-body px-0 py-0">
				<div class="row">
					<div class="col-12">
						<div class="content content-wrapper">
							<div class="clearfix"></div>

							@include('flash::message')

							<div class="clearfix"></div>
							<div class="box box-primary">
								<div class="box-body">
									<div id="import-instructions" class="justify-content-center ml-2 mb-2">
										<input type="button" class="btn btn-primary" value="{{ __('app.Download Template File') }}" onclick="downloadTempFile()">
										<input type="button" class="btn btn-primary" value="{{ __('app.Use An Existing File') }}" onclick="useTempFile()">
									</div>
									<div id="import-form">
										@include('services.import.form')
									</div>
									<div id="import-information">
										@include('services.import.information')
									</div>
									<input type="hidden" id="template-file-download" value="0">
								</div>
							</div>
							<div class="text-center">

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('additionalscripts')
<script type="text/javascript">
	function setScreen() {
		if ($("#template-file-download").val() == 0) {
			$('#import-instructions').show();
			$('#import-form').hide();
		} else {
			//$('#import-instructions').hide();
			$('#import-form').show();
		}
	}

	function downloadTempFile() {
		window.location.href = "{{ url('/admin/services/import/download-file') }}";
		$("#template-file-download").val(1);

		setTimeout(setScreen, 3000);
	}

	function useTempFile() {
		$("#template-file-download").val(1);
		setScreen();
	}

	setScreen();
</script>
@endsection