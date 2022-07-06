@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-6 col-md-4 col-lg-3"><a href="{{ URL::to('admin/settings/partners') }}">Back </a></div>
					<div class="col-6 col-md-8 col-lg-9 px-3 px-md-5 d-none d-md-block">{{ $partner->langs[0]->name }}</div>
					<div class="col-6 col-md-8 col-lg-9 px-3 px-md-5 text-right d-block d-md-none">{{ $partner->langs[0]->name }}</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12 col-md-4 col-lg-3">
						@if(isset($partner->logo) && strlen($partner->logo) > 0)
						<img id="preview_image" class="preview" src="{{ URL::to('/') }}/media/{{ $partner->logo->filename }}" alt="" style="width: 100%">
						@else
						<img id="preview_image" class="preview" src="{{ URL::to('/') }}/media/noimage.png" alt="" style="width: 100%">
						@endif
					</div>
					<div class="col-12 col-md-8 col-lg-9 mt-5 mt-md-0 px-3 px-md-5">
						<h3>{{ $partner->langs[0]->full_name }}</h3>
						<h4> {{ $partner->type != null ? $partner->type->langs[0]->name : '' }}</h4>
						<p>{{ $partner->langs[0]->url }}</p>
						<p>{!! $partner->langs[0]->description !!}</p>
					</div>
				</div>
				<div class="row mt-4 mb-2">
					<div class="col-12">
						<h4 class="text-green">People</h4>
					</div>
					<div class="col-12">
						<table class="table table-bordered dataTable no-footer">
							<thead>
								<tr>
									<td>Id</td>
									<td>Name</td>
									<td>Feedback Terms</td>
								</tr>
							</thead>
							<tbody>
								@foreach($users as $user)
								<tr>
									<td>{{$user->id}}</td>
									<td><a href="{{ URL::to('admin/profile/'.$user->username) }}">{{$user->name}}</a></td>
									<td>{{$user->feedbackTerms}}</td>
								</tr>
								@endforeach

							</tbody>
						</table>


					</div>
				</div>

				<div class="row mt-4 mb-2">
					<div class="col-12">
						<h4 class="text-green">Services</h4>
					</div>
					<div class="col-12">
						<table class="table table-bordered dataTable no-footer">
							<thead>
								<tr>
									<td>Id</td>
									<td>Province</td>
									<td>District</td>
									<td>Category</td>
									<td>Start</td>
									<td>End</td>
								</tr>
							</thead>
							<tbody>
								@foreach($services as $service)
								<tr>
									<td><a href="{{ URL::to('admin/services/services/'.$service->id) }}">{{$service->id}}</a></td>
									<td>
										@if($service->location != null && $service->location->city != null && $service->location->city->langs != null && count($service->location->city->langs) > 0)
											{{ $service->location->city->langs[0]->name }}
										@endif
									</td>
									<td>
										@if($service->location != null && $service->location->district != null && $service->location->district->langs != null && count($service->location->district->langs) > 0)
											{{ $service->location->district->langs[0]->name }}
										@endif
									</td>
									<td>
										<?php
											$cats = [];
											if($service != null && $service->categories != null && count($service->categories) > 0) {
												foreach ($service->categories as $category) {
													if($category->term != null && $category->term->langs && count($category->term->langs) > 0 && !$category->deleted) {
														array_push($cats, '<span class="service-category-item term-'.$category->term->id.'">' . $category->term->langs[0]->name . '</span>');
													}
												}
											}

											echo implode('', $cats);
										?>
									</td>
									<td>{{date('d/m/Y', strtotime($service->start_date))}}</td>
									<td>{{date('d/m/Y', strtotime($service->end_date))}}</td>
								</tr>
								@endforeach

							</tbody>
						</table>


					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
