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


/* User */
Route::get('user', ['uses' => 'UserController@getAuthenticatedUser']);
Route::post('user', ['uses' => 'UserController@signup']);
Route::post('user/signin', ['uses' => 'UserController@signin']);
Route::post('user/signout', ['uses' => 'UserController@signout'/*, 'middleware' => 'auth.jwt'*/]);
Route::post('user/signin', ['uses' => 'UserController@signin']);

Route::post('password/email', ['uses' => 'PasswordResetController@email']);

Route::post('password/reset', ['uses' => 'PasswordResetController@reset']);

// Route::get('user/contacts', ['uses' => 'UserController@contacts', 'middleware' => 'auth.jwt']);


/* Posts */
Route::get('posts', ['uses' => 'PostController@index']);

Route::get('posts/{id}', ['uses' => 'PostController@show']);

Route::get('post/{slug}', ['uses' => 'PostController@getBySlug']);

Route::post('posts', ['uses' => 'PostController@store', 'middleware' => 'auth.jwt']);

Route::put('posts/{id}', ['uses' => 'PostController@update', 'middleware' => 'auth.jwt']);

Route::delete('posts/{id}', ['uses' => 'PostController@destroy', 'middleware' => 'auth.jwt']);


/* Sections */
Route::get('sections', ['uses' => 'SectionController@index']);

Route::get('sections/{id}', ['uses' => 'SectionController@show']);

Route::post('sections', ['uses' => 'SectionController@store', 'middleware' => 'auth.jwt']);

Route::put('sections/{id}', ['uses' => 'SectionController@update', 'middleware' => 'auth.jwt']);

Route::delete('sections/{id}', ['uses' => 'SectionController@destroy', 'middleware' => 'auth.jwt']);


/* Tags */
Route::get('tags', ['uses' => 'TagController@index']);

Route::get('tags/{id}', ['uses' => 'TagController@show']);

Route::post('tags', ['uses' => 'TagController@store', 'middleware' => 'auth.jwt']);

Route::put('tags/{id}', ['uses' => 'TagController@update', 'middleware' => 'auth.jwt']);

Route::delete('tags/{id}', ['uses' => 'TagController@destroy', 'middleware' => 'auth.jwt']);

Route::get('tags/search/{query}', ['uses' => 'TagController@search']);


/* Products */
Route::get('products', ['uses' => 'ProductController@index']);

Route::get('products/{id}', ['uses' => 'ProductController@show']);

Route::post('products', ['uses' => 'ProductController@store', 'middleware' => 'auth.jwt']);

Route::put('products/{id}', ['uses' => 'ProductController@update', 'middleware' => 'auth.jwt']);

Route::delete('products/{id}', ['uses' => 'ProductController@destroy', 'middleware' => 'auth.jwt']);


/* Images */
Route::post('images', ['uses' => 'ImageController@store', 'middleware' => 'auth.jwt']);
Route::post('images/{id}', ['uses' => 'ImageController@update', 'middleware' => 'auth.jwt']);
Route::delete('images/{id}', ['uses' => 'ImageController@destroy', 'middleware' => 'auth.jwt']);


/* Events */
Route::get('events', ['uses' => 'EventController@index']);
Route::post('events', ['uses' => 'EventController@store']);
Route::put('events/{id}', ['uses' => 'EventController@update']);
Route::delete('events/{id}', ['uses' => 'EventController@destroy']);

/* Messages */
// Route::get('messages', ['uses' => 'MessageController@index']);
Route::get('messages/conversations', ['uses' => 'MessageController@conversations', 'middleware' => 'auth.jwt']);
Route::get('messages/{id}', ['uses' => 'MessageController@index', 'middleware' => 'auth.jwt']);
Route::post('messages', ['uses' => 'MessageController@store', 'middleware' => 'auth.jwt']);
Route::put('messages/{id}', ['uses' => 'MessageController@update', 'middleware' => 'auth.jwt']);
Route::delete('messages/{id}', ['uses' => 'MessageController@destroy', 'middleware' => 'auth.jwt']);


/* PAYPAL */
Route::post('paypal', ['uses' => 'PaypalController@checkout']);
Route::post('paypal/execute', ['uses' => 'PaypalController@execute']);


/* STRIPE */
Route::post('stripe', ['uses' => 'StripeController@checkout']);