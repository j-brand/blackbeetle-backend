<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\AlbumController;
use App\Http\Controllers\Frontend\StoryController;
use App\Http\Controllers\Frontend\SubscriptionController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\OptionController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/album', [AlbumController::class, 'getAlbums']);
Route::get('/album/{slug}', [AlbumController::class, 'getAlbumBySlug']);

Route::get('/story', [StoryController::class, 'getStories']);
Route::get('/story/{slug}/{order?}', [StoryController::class, 'getStoryBySlug']);

Route::post('/comment', [CommentController::class, 'store']);

Route::post('/newsletter', [SubscriptionController::class, 'create']);
Route::post('/verify-email', [SubscriptionController::class, 'verify']);
Route::post('/resend-verification', [SubscriptionController::class, 'resend']);
Route::get('/subscriptions/{token}', [SubscriptionController::class, 'getSubscriptions']);
Route::post('/subscription', [SubscriptionController::class, 'updateSubscription']);

Route::get('/option/my_location', [OptionController::class, 'getLocation']);
