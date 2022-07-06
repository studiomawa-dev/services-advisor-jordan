<?php

Route::get('/', function () {
	return view('index');
});

Route::group(['namespace' => 'App\\Http\\Controllers\\Admin'], function () {
	Auth::routes(['register' => false]);
});

Route::get('logout', '\App\Http\Controllers\Admin\Auth\LoginController@logout');

Route::group(
	['prefix' => 'admin', 'middleware' => 'admin', 'namespace' => 'App\\Http\\Controllers\\Admin'],
	function () {

		Route::get('/', 'DashboardController@index');

		Route::get('/dashboard', 'DashboardController@index');

		Route::get('/language/{locale}', function ($locale) {

			app()->setLocale($locale);
			session()->put('applocale', $locale);
			return redirect()->back();
		});

		Route::group(['prefix' => 'import'], function () {
			Route::get('index', 'Import\IndexController@index')->name('import.index');
			Route::get('partners', 'Import\PartnersController@index')->name('import/partners');
			Route::get('terms', 'Import\TermsController@index')->name('import/terms');
			Route::get('terms-arabic', 'Import\TermsController@arabic')->name('import/terms-arabic');
			Route::get('category', 'Import\CategoryController@index')->name('import/category');
			Route::get('location-terms', 'Import\LocationController@terms')->name('import/location-terms');
			Route::get('locations', 'Import\LocationController@index')->name('import/locations');
			Route::get('users', 'Import\UserController@index')->name('import/user');
			Route::get('services', 'Import\ServiceController@index')->name('import/services');
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('roles', 'Settings\RoleController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('seo', 'Settings\SeoController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('languages', 'Settings\LanguageController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('logs', 'Settings\LogController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'definitions'], function () {
			Route::resource('taxonomies', 'Definitions\TaxonomyController', ["as" => 'definitions']);
		});

		Route::group(['prefix' => 'definitions'], function () {
			Route::resource('terms', 'Definitions\TermController', ["as" => 'definitions']);
		});

		Route::group(['prefix' => 'services'], function () {
			Route::get('services.export', 'Services\ServiceController@export')->name('services.export');
			Route::resource('services', 'Services\ServiceController', ["as" => 'services']);
		});

		Route::group(['prefix' => 'services'], function () {
			Route::get('import', 'Services\ImportController@index')->name('services.import');
			Route::get('import/download-file', 'Services\ImportController@downloadTemp');
			Route::post('import/preview', 'Services\ImportController@preview');
			Route::post('import/process', 'Services\ImportController@process')->name('services.import.process');
		});

		Route::group(['prefix' => 'services'], function () {
			Route::match(array('GET', 'POST'), 'ervices/delete-multiple', 'Services\ServiceController@deleteMultiple')->name('services.delete-multiple');
			Route::resource('services', 'Services\ServiceController', ["as" => 'services']);
		});

		Route::group(['prefix' => 'services'], function () {
			Route::get('locations/item/{id}', 'Services\LocationController@item');
			Route::get('locations/list', 'Services\LocationController@list');
			Route::resource('locations', 'Services\LocationController', ["as" => 'services']);
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('partners', 'Settings\PartnerController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'contents'], function () {
			Route::resource('pages', 'Contents\PageController', ["as" => 'contents']);
		});

		Route::group(['prefix' => 'contents'], function () {
			Route::post('medias/upload', 'Contents\MediaController@upload')->middleware('auth');
			Route::resource('medias', 'Contents\MediaController', ["as" => 'contents']);
		});

		Route::group(['prefix' => 'inbox'], function () {
			Route::resource('messages', 'Inbox\MessageController', ["as" => 'inbox']);

			Route::get('conversations', 'Inbox\ConversationController@index');
			Route::post('conversations', 'Inbox\ConversationController@store');
			Route::get('conversations/{conversation}/users', 'Inbox\ConversationController@participants');
			Route::post('conversations/{conversation}/users', 'Inbox\ConversationController@join');
			Route::delete('conversations/{conversation}/users', 'Inbox\ConversationController@leaveConversation');
			Route::get('conversations/{conversation}/messages', 'Inbox\ConversationController@getMessages');
			Route::post('conversations/{conversation}/messages', 'Inbox\ConversationController@sendMessage');
			Route::delete('conversations/{conversation}/messages', 'Inbox\ConversationController@deleteMessages');
		});

		Route::get('profile/{username}', 'Settings\UserController@profile');
		Route::get('photo/{username}', 'Settings\UserController@photo');
		Route::get('me', 'Settings\UserController@me');

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('users', 'Settings\UserController', ["as" => 'settings']);
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::resource('notifications', 'Settings\NotificationController', ["as" => 'settings']);
		});

		Route::get('clear-cache', function () {
			Artisan::call('cache:clear');
			Flash::success(__('app.Application cache successfully cleared!'));
			return redirect('/admin');
		});
	}
);

Route::any('{path?}', function ($path) {
	return view("index");
})->where("path", "^(tr|en|ku|ar|ps|fa|it|fr).*");
