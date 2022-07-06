<li class="nav-item">
	<a href="{{ url('/admin') }}" class="nav-link"><i class="link-icon mdi mdi-television"></i><span class="menu-title">{{ __('app.DASHBOARD') }}</span></a>
</li>

<li class="nav-item">
	<a href="#services" class="nav-link"><i class="link-icon mdi mdi-asterisk"></i><span class="menu-title">{{ __('app.SERVICES') }}</span><i class="menu-arrow"></i></a>
	<div class="submenu">
		<ul class="submenu-item">
			@if(!Auth::user()->isInRole('viewer'))
			<li class="nav-item ">
				<a href="{!! route('services.services.create') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Add New') }}</span></a>
			</li>
			@endif
			<li class="nav-item">
				<a href="{!! route('services.services.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Services List') }}</span></a>
			</li>
			@if(Auth::user()->isInRole('sysadmin'))
			<li class="nav-item">
				<a href="{!! route('services.import') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Import Services') }}</span></a>
			</li>
			<li class="nav-item">
				<a href="{!! route('services.delete-multiple') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Delete Services') }}</span></a>
			</li>
			@endif
		</ul>
	</div>
</li>

<li class="nav-item">
	<a href="#content" class="nav-link"><i class="link-icon mdi mdi-flag-outline"></i><span class="menu-title">{{ __('app.LOCATIONS') }}</span><i class="menu-arrow"></i></a>
	<div class="submenu">
		<ul class="submenu-item">
			@if(!Auth::user()->isInRole('viewer'))
			<li class="nav-item ">
				<a href="{!! route('services.locations.create') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Add New') }}</span></a>
			</li>
			@endif
			<li class="nav-item">
				<a href="{!! route('services.locations.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Location List') }}</span></a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a href="{!! route('settings.partners.index') !!}" class="nav-link"><i class="link-icon mdi mdi-trophy-outline"></i><span class="menu-title">{{ __('app.ORGANIZATIONS') }}</span></a>
</li>
<li class="nav-item mega-menu definition-menu">
	<a href="#definitions" class="nav-link"><i class="link-icon mdi mdi-atom"></i><span class="menu-title">{{ __('app.TAXONOMY') }}</span><i class="menu-arrow"></i></a>
	<div class="submenu">
		<ul class="submenu-item">
			@if(isset($taxonomies))
			@foreach ($taxonomies as $taxonomy)
			@if($taxonomy != null && isset($taxonomy->name))
			<li class="nav-item">
				<a class="nav-link" href="{!! route('definitions.terms.index', ['taxonomy' => $taxonomy->id]) !!}"">
								{{ $taxonomy->name }}
							</a>
						</li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</li>
<!--
<li class=" nav-item">
					<a href="{!! route('inbox.messages.index') !!}" class="nav-link"><i class="link-icon mdi mdi-mailbox-open-outline"></i><span class="menu-title">INBOX</span></a>
			</li>-->

			@if(Auth::user()->isAdmin() || Auth::user()->isSysAdmin())
			<li class="nav-item">
				<a href="#settings" class="nav-link"><i class="link-icon mdi mdi-settings-outline"></i><span class="menu-title">{{ __('app.SETTINGS') }}</span><i class="menu-arrow"></i></a>
				<div class="submenu">
					<ul class="submenu-item">
						<li class="nav-item">
							<a href="{!! route('settings.users.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Users') }}</span></a>
						</li>
						<li class="nav-item">
							<a href="{!! route('settings.roles.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Roles') }}</span></a>
						</li>
						@if(Auth::user()->isSysAdmin())
						<li class="nav-item">
							<a href="{!! route('settings.languages.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Languages') }}</span></a>
						</li>
						<li class="nav-item">
							<a href="{!! route('settings.notifications.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Notifications') }}</span></a>
						</li>
						<li>
							<a href="{!! route('settings.logs.index') !!}" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Logs') }}</span></a>
						</li>
						@endif
						<li>
							<a href="/admin/clear-cache" class="nav-link"><i class="fa fa-edit"></i><span>{{ __('app.Clear Cache') }}</span></a>
						</li>
					</ul>
				</div>
			</li>
			@endif