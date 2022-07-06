<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('cors')->get('partners', '\App\Http\Controllers\API\PartnerAPIController@list');
Route::middleware('cors')->get('terms/locations', '\App\Http\Controllers\API\TermAPIController@locations');
Route::middleware('cors')->get('terms/categories', '\App\Http\Controllers\API\TermAPIController@categories');
Route::middleware('cors')->get('terms/nationalities', '\App\Http\Controllers\API\TermAPIController@nationalities');
Route::middleware('cors')->get('services/data', '\App\Http\Controllers\API\ServiceAPIController@data');
Route::middleware('cors')->get('services/initialdata', '\App\Http\Controllers\API\ServiceAPIController@initialData');

Route::middleware('cors')->get('data', '\App\Http\Controllers\API\ServiceAPIController@data');
Route::middleware('cors')->get('services', '\App\Http\Controllers\API\ServiceAPIController@services');
Route::middleware('cors')->get('categories', '\App\Http\Controllers\API\ServiceAPIController@categories');

Route::middleware('cors')->get('services/coordinates', '\App\Http\Controllers\API\ServiceAPIController@coordinates');
Route::middleware('cors')->get('services/clusters', '\App\Http\Controllers\API\ServiceAPIController@clusters');
Route::middleware('cors')->get('services/terms', '\App\Http\Controllers\API\ServiceAPIController@terms');
Route::middleware('cors')->get('services/item/{id}', '\App\Http\Controllers\API\ServiceAPIController@item');
Route::middleware('cors')->get('services/version', '\App\Http\Controllers\API\ServiceAPIController@version');
Route::middleware('cors')->get('services/feedback', '\App\Http\Controllers\API\ServiceAPIController@feedback');
Route::middleware('cors')->get('notifications/list', '\App\Http\Controllers\API\NotificationAPIController@list');

Route::middleware('cors')->post('services/list', '\App\Http\Controllers\API\ServiceAPIController@list');
Route::middleware('cors')->get('services/setLocationLangs', '\App\Http\Controllers\API\ServiceAPIController@setLocationLangs');
