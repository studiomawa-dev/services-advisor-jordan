<nav class="navbar horizontal-layout col-lg-12 col-12 p-0">
	<div class="container-fluid d-flex flex-row">
		<div class="text-center navbar-brand-wrapper d-flex align-items-top">
			<a class="navbar-brand brand-logo" href="{{ url('/admin') }}"><img src="{{ url('/img/logo.png') }}" alt="logo" /><span style="font-size: 20px;font-weight: bold;margin-left: 10px;line-height: 1;color: #5dacac;">SERVICES ADVISOR</span></a>
			<a class="navbar-brand brand-logo-mini" href="{{ url('/admin') }}"><img src="{{ url('/img/logo.png') }}" alt="logo" /></a>
		</div>
		<div class="navbar-menu-wrapper d-flex align-items-center">
			<form class="search-field ml-auto" action="#">
				<div class="form-group mb-0  d-none">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
						</div>
						<input type="text" class="form-control">
					</div>
				</div>
			</form>
			<ul class="navbar-nav navbar-nav-right mr-0">
				<li class="nav-item dropdown ml-4 d-none">

					<a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
						<i class="mdi mdi-bell-outline"></i>
						<span class="count bg-warning">4</span>
					</a>
					<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
						<a class="dropdown-item py-3">
							<p class="mb-0 font-weight-medium float-left">You have 4 new notifications
							</p>
							<span class="badge badge-pill badge-inverse-info float-right">View all</span>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item preview-item">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-inverse-success">
									<i class="mdi mdi-alert-circle-outline mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal text-dark mb-1">Application Error</h6>
								<p class="font-weight-light small-text mb-0">
									Just now
								</p>
							</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item preview-item">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-inverse-warning">
									<i class="mdi mdi-comment-text-outline mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal text-dark mb-1">Settings</h6>
								<p class="font-weight-light small-text mb-0">
									Private message
								</p>
							</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item preview-item">
							<div class="preview-thumbnail">
								<div class="preview-icon bg-inverse-info">
									<i class="mdi mdi-email-outline mx-0"></i>
								</div>
							</div>
							<div class="preview-item-content">
								<h6 class="preview-subject font-weight-normal text-dark mb-1">New user registration</h6>
								<p class="font-weight-light small-text mb-0">
									2 days ago
								</p>
							</div>
						</a>
					</div>
				</li>
				<li class="nav-item">
					<div id="translator-container">
						@include('settings.languages.switcher')
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
						<img class="img-xs rounded-circle" src="{{ URL::to('/') }}/admin/photo/{{ Auth::user()->username }}?t={{ time() }}" alt="{!! Auth::user()->name !!}">
					</a>
					<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
						<a class="dropdown-item p-0">
							<div class="d-flex border-bottom w-100">
								<div class="py-3 px-4 d-flex align-items-center justify-content-center flex-grow-1"><i class="mdi mdi-bookmark-plus-outline mr-0 text-gray"></i></div>
								<div class="py-3 px-4 d-flex align-items-center justify-content-center border-left border-right flex-grow-1"><i class="mdi mdi-account-outline mr-0 text-gray"></i></div>
								<div class="py-3 px-4 d-flex align-items-center justify-content-center flex-grow-1"><i class="mdi mdi-alarm-check mr-0 text-gray"></i></div>
							</div>
						</a>
						<a class="dropdown-item" href="{{ url('admin/me') }}">
							{!! Auth::user()->name !!}
						</a>
						<a class="dropdown-item" href="{{ url('admin/inbox/messages') }}">
							Inbox
						</a>
						<a href="{!! url('/logout') !!}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							Sign Out
						</a>
						<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</div>
				</li>
			</ul>
			<button class="navbar-toggler align-self-center" type="button" data-toggle="minimize">
				<span class="mdi mdi-menu"></span>
			</button>
		</div>
	</div>
	<div class="nav-bottom">
		<div class="container-fluid">
			<ul class="nav page-navigation">
				@include('layouts.menu')
			</ul>
		</div>
	</div>
</nav>