@extends('layouts.app')

@section('content')
<div class="">

	<div class="row">
		<div class="col-12 grid-margin">
			<div class="card card-statistics">
				<div class="row">
					<div class="card-col col-xl-3 col-lg-3 col-md-3 col-6" onclick="location.href='/admin/services/services'">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
								<i class="mdi mdi-star-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
								<div class="wrapper text-center text-sm-left">
									<p class="card-text text-green mb-0">{{ __('app.Services') }}</p>
									<div class="fluid-container">
										<h3 class="card-title mb-0">{{ number_format($serviceCount) }}</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-col col-xl-3 col-lg-3 col-md-3 col-6" onclick="location.href='/admin/settings/partners'">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
								<i class="mdi mdi-trophy-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
								<div class="wrapper text-center text-sm-left">
									<p class="card-text text-green mb-0">{{ __('app.Organizations') }}</p>
									<div class="fluid-container">
										<h3 class="card-title mb-0">{{ number_format($partnerCount) }}</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-col col-xl-3 col-lg-3 col-md-3 col-6" onclick="location.href='/admin/services/locations'">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
								<i class="mdi mdi-target text-primary mr-0 mr-sm-4 icon-lg"></i>
								<div class="wrapper text-center text-sm-left">
									<p class="card-text text-green mb-0">{{ __('app.Locations') }}</p>
									<div class="fluid-container">
										<h3 class="card-title mb-0">{{ number_format($locationCount) }}</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-col col-xl-3 col-lg-3 col-md-3 col-6" onclick="location.href='/admin/settings/users'">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
								<i class="mdi mdi-account-outline text-primary mr-0 mr-sm-4 icon-lg"></i>
								<div class="wrapper text-center text-sm-left">
									<p class="card-text text-green mb-0">{{ __('app.Users') }}</p>
									<div class="fluid-container">
										<h3 class="card-title mb-0">{{ number_format($userCount) }}</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title text-green">{{ __('app.Recent Updates') }}</h5>
					<ul class="bullet-line-list" style="overflow-y: scroll; max-height: 250px;">
						@if($updates != null && count($updates)>0)
						@foreach($updates as $update)
						<li>
							<h6>{{ $update->statusText }}</h6>
							<p class="mb-0">
								<a href="{{ url($update->itemLink) }}">{{$update->itemText}}</a>
							</p>
							<p class="text-muted">
								<a href="{{ url('admin/profile/' . $update->user['username']) }}">{{ $update->user['name'] }}</a>,
								<i class="mdi mdi-clock"></i>
								<?= date('d.m.Y H:i', $update->actionTime) ?>
							</p>
						</li>
						@endforeach
						@endif
					</ul>
				</div>
			</div>
		</div>

		<div class="col-sm-6 grid-margin stretch-card">
			<div class="card">
				<div class="card-body pb-0">
					<div class="wrapper">
						<h5 class="mb-0 text-green">{{ __('app.Top Service Categories') }}</h5>
					</div>
				</div>
				<canvas id="current-chart" height="320" class="mb-3"></canvas>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title text-green">{{ __('app.Recent Services') }}</h5>
					<table class="services-table table table-bordered dataTable no-footer" width="100%" style="width: 100%;">
						<thead>
							<tr role="row">
								<th title="Id">{{ __('app.Id') }}</th>
								<th title="Province">{{ __('app.Province') }}</th>
								<th title="District">{{ __('app.District') }}</th>
								<th title="Partner">{{ __('app.Organization') }}</th>
								<th title="Category">{{ __('app.Category') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($services as $service)
							<tr>
								<td>
									@if((Auth::user()->isAdmin() || Auth::user()->isInPartner($service->partner)) && !Auth::user()->isInRole('viewer'))
									<a href="{{ url('admin/services/services/' . $service->id . '/edit') }}">{{ $service->id }} <i class="mdi mdi-pencil"></i></a>
									@else
									{{ $service->id }}
									@endif

								</td>
								<td>{{ $service->location->city->langs[0]->name }}</td>
								<td>{{ $service->location->district->langs[0]->name }}</td>
								<td>
									@if($service->partner != null && count($service->partner->langs) > 0)
									<a href="{{ url('admin/settings/partners/' . $service->partner_id) }}">{{ $service->partner->langs[0]->name }}</a>
									@endif
								</td>
								<td>
									@if(count($service->categories) > 0)
									@foreach($service->categories as $category)
									@if(!$category->deleted)
									<span class="service-category-item term-{{ $category->term->id }}">{{ $category->term->langs[0]->name }}</span>
									@endif
									@endforeach
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	var serviceCategoryCounts = JSON.parse('<?= json_encode($serviceCategoryCounts) ?>');
	var colors = [{
		id: 1609,
		code: '#a49869'
	}, {
		id: 1736,
		code: '#9e1213'
	}, {
		id: 1645,
		code: '#5b951c'
	}, {
		id: 1665,
		code: '#b7196e'
	}, {
		id: 1687,
		code: '#984d4b'
	}, {
		id: 1621,
		code: '#d4591e'
	}, {
		id: 1787,
		code: '#0099d3'
	}, {
		id: 1631,
		code: '#e79810'
	}, {
		id: 1638,
		code: '#403f8f'
	}];

	if ($("#current-chart").length && serviceCategoryCounts.length) {
		var categories = serviceCategoryCounts.map(x => x.name);
		var colors = serviceCategoryCounts.map(x => getColor(x.term_id))
		var counts = serviceCategoryCounts.map(x => x.count);
		var max = Math.max.apply(Math, counts);
		console.log(max);

		var CurrentChartCanvas = $("#current-chart").get(0).getContext("2d");
		var CurrentChart = new Chart(CurrentChartCanvas, {
			type: 'bar',
			data: {
				labels: categories,
				datasets: [{
					label: 'Service',
					data: counts,
					backgroundColor: colors
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				layout: {
					padding: {
						left: 0,
						right: 0,
						top: 20,
						bottom: 0
					}
				},
				scales: {
					yAxes: [{
						display: false,
						gridLines: {
							display: false
						}
					}],
					xAxes: [{
						stacked: true,
						ticks: {
							beginAtZero: true,
							fontColor: "#354168",

							autoSkip: false,
							maxRotation: 90,
							minRotation: 0
						},
						gridLines: {
							color: "rgba(0, 0, 0, 0)",
							display: false
						},
						barPercentage: 0.4
					}]
				},
				legend: {
					display: false
				},
				elements: {
					point: {
						radius: 0
					}
				}
			}
		});
	}

	function getColor(id) {
		console.log(id);
		for (var i = 0; i < colors.length; i++) {
			var color = colors[i];
			if (color.id == id) {
				return color.code;
			}
		}
		return '#5dacac';
	}
</script>
@endsection