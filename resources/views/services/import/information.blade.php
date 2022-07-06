<div class="card index-card">
	<div class="card-header">
		<h4 class="card-title my-1">{!! __('app.Template File Information') !!}</h4>
		<p>{!! __('app.You can import services on this page') !!}. {{ __('app.Please read the excel file column information below before start import process') }}.</p>
		<h5 class="text-danger">{!! __('app.Attention!') !!} {!! __('app.Do not modify the Template File Headers!') !!}</h5>
	</div>
	<div class="card-body px-0 py-0">
		<div class="row">
			<div class="col-12">
				<div class="content content-wrapper">
					<div class="clearfix"></div>
					<table class="table table-condensed table-bordered table-striped">
						<tr>
							<th>Start Date</th>
							<td>{{ __('app.The date for the service will shown on the map') }}. {{ __('app.Valid format') }} <code>YYYY-MM-DD</code></td>
						</tr>
						<tr>
							<th>End Date</th>
							<td>{{ __('app.The date for the service will stop showing on the map') }}. {{ __('app.Valid format') }} <code>YYYY-MM-DD</code></td>
						</tr>
						<tr>
							<th>Organization</th>
							<td>{{ __('app.Organization name of the service') }}. {{ __('app.Should be the exact name of any organization name') }}. <a href="{!! route('settings.partners.index') !!}" target="_blank">{{ __('app.Click to see organizations') }}</a></td>
						</tr>
						<tr>
							<th>Categories</th>
							<td>{{ __('app.Category name should be exact name of categories') }}. {{ __('app.For multiple categories you can add semicolon(;) between category names.') }} <a href="{!! route('definitions.terms.index', ['taxonomy' => 12]) !!}" target="_blank">{{ __('app.Click to see all available categories') }}</a></td>
						</tr>
						<tr>
							<th>Availability</th>
							<td>{{ __('app.Valid format') }} <code>DAY_NAME-HOUR_START-HOUR_END</code>. {{ __('app.Example') }}: <code>Monday-09:00-18:00</code>. {{ __('app.For multiple days you can add semicolon(;) between items.') }} {{ __('app.Example') }}: <code>Monday-09:00-18:00;Tuesday-10:00-14:00</code>. </td>
						</tr>
						<tr>
							<th>Accessibility</th>
							<td>{{ __('app.Should be exact name of any Accessibility terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items.') }} {{ __('app.Example') }}: <code>Inpatient;Referrals only;Hotline and follow up home visit</code> <a href="{!! route('definitions.terms.index', ['taxonomy' => 1]) !!}" target="_blank">{{ __('app.Click to see all accessibility terms') }}</a></td>
						</tr>
						<tr>
							<th>City</th>
							<td>{{ __('app.Current city name of the service') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 13]) !!}" target="_blank">{{ __('app.Click to see all available cities') }}</a></td>
						</tr>
						<tr>
							<th>District</th>
							<td>{{ __('app.Current district name of the service') }}. {{ __('app.District name should be match with city name') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 13]) !!}" target="_blank">{{ __('app.Click to see all available districts') }}</a></td>
						</tr>
						<tr>
							<th>Latitude</th>
							<td>{{ __('app.Latitude coordinate of the service location') }}. <a href="{!! route('services.locations.create') !!}" target="_blank">{{ __('app.Click to find correct latitude on map') }}</a></td>
						</tr>
						<tr>
							<th>Longitude</th>
							<td>{{ __('app.Longitude coordinate of the service location') }}. <a href="{!! route('services.locations.create') !!}" target="_blank">{{ __('app.Click to find correct longitude on map') }}</a></td>
						</tr>
						<tr>
							<td colspan="2">{{ __('app.Info') }}: {{ __('app.Service location record will automaticly create if not exists') }}. {{ __('app.Please use the correct values of City, District, Latitude and Longitude for an exists location') }}.</td>
						</tr>
						<tr>
							<th>Legal Documents Required</th>
							<td>{{ __('app.Should be exact name of any Legal Documents Required terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 16]) !!}" target="_blank">Click to see all Legal Documents Required terms</a></td>
						</tr>
						<tr>
							<th>Nationality</th>
							<td>{{ __('app.Should be exact name of any Nationality terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 3]) !!}" target="_blank">Click to see all Nationality terms</a></td>
						</tr>
						<tr>
							<th>Gender & Age</th>
							<td>{{ __('app.Should be exact name of any Gender & Age terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. Valid Items: <code>Adults, Boys, Girls, Men, Women</code></td>
						</tr>
						<tr>
							<th>Intake Criteria</th>
							<td>{{ __('app.Should be exact name of any Intake Criteria terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 8]) !!}" target="_blank">Click to see all Intake Criteria terms</a></td>
						</tr>
						<tr>
							<th>Coverage</th>
							<td>{{ __('app.Should be exact name of any Coverage terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 5]) !!}" target="_blank">Click to see all Coverage terms</a></td>
						</tr>
						<tr>
							<th>Referral Method</th>
							<td>{{ __('app.Should be exact name of any Referral Method terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 14]) !!}" target="_blank">Click to see all Referral Method terms</a></td>
						</tr>
						<tr>
							<th>Immediate Next Step After Referral</th>
							<td>{{ __('app.Should be exact name of any Immediate Next Step After Referral terms') }}. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 15]) !!}" target="_blank">Click to see all Immediate Next Step After Referral terms</a></td>
						</tr>
						<tr>
							<th>Response Delay After Referral</th>
							<td>{{ __('app.Should be exact name of any Response Delay After Referral terms') }}. {{ __('app.Only one item allowed') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 17]) !!}" target="_blank">Click to see all Response Delay After Referral terms</a></td>
						</tr>
						<tr>
							<th>Feedback Mechanism</th>
							<td>Should be exact name of any Feedback Mechanism terms. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 7]) !!}" target="_blank">Click to see all Feedback Mechanism terms</a></td>
						</tr>
						<tr>
							<th>Feedback Delay</th>
							<td>Should be exact name of any Feedback Delay terms. Only one item allowed. <a href="{!! route('definitions.terms.index', ['taxonomy' => 6]) !!}" target="_blank">Click to see all Feedback Delay terms</a></td>
						</tr>
						<tr>
							<th>Complaints Mechanism</th>
							<td>Should be exact name of any Complaints Mechanism terms. {{ __('app.For multiple values you can add semicolon(;) between items') }}. <a href="{!! route('definitions.terms.index', ['taxonomy' => 4]) !!}" target="_blank">Click to see all Complaints Mechanism terms</a></td>
						</tr>
						<tr>
							<th>Additional Details <sub>(Optional)</sub></th>
							<td>You can write down for any additional details for the service.</td>
						</tr>
						<tr>
							<th>Comments <sub>(Optional)</sub></th>
							<td>You can write down for any comments for the service.</td>
						</tr>
						<tr>
							<th>Hotline / Public Phone</th>
							<td>Services hotline or public phone number</td>
						</tr>
						<tr>
							<th>More Info Link</th>
							<td>You can add a mode info website link of the service</td>
						</tr>
					</table>


				</div>
			</div>
		</div>
	</div>
</div>