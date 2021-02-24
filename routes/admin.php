<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'authenticate']);
Route::post('/auth/logout', [AuthController::class, 'logout']);


/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/test', [AlbumController::class, 'index']);

    /*
    | User routes - Benutzer
    */
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'get']);
        Route::post('/{id}', [UserController::class, 'update']);
        Route::post('/', [UserController::class, 'store']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/sendverification/{id}', [UserController::class, 'sendVerification']);
    });

    /*
    | Albums routes - Album
    */
    Route::group(['prefix' => 'album'], function () {
        Route::get('/', [AlbumController::class, 'index']);
        Route::get('/{id}', [AlbumController::class, 'get']);
        Route::post('/create', [AlbumController::class, 'store']);
        Route::post('/update/{id}', [AlbumController::class, 'update']);
        Route::delete('/{id}', [AlbumController::class, 'destroy']);
        Route::post('/upload/{id}', [AlbumController::class, 'upload']);
        Route::post('/{id}/position', [AlbumController::class, 'changeImagePosition']);
        Route::delete('/image/{id}', [AlbumController::class, 'deleteImage']);
    });

    /*
    | Story routes - Geschichten
    */
    Route::group(['prefix' => 'story'], function () {
        Route::get('/', [StoryController::class, 'index']);
        Route::get('/{id}', [StoryController::class, 'get']);
        Route::post('/create', [StoryController::class, 'store']);
        Route::post('/update/{id}', [StoryController::class, 'update']);
        Route::delete('/{id}', [StoryController::class, 'destroy']);
        Route::post('/upload/{id}', [StoryController::class, 'upload']);
    });

    /*
    | Post routes - Beiträge
    */
    Route::group(['prefix' => 'post'], function () {
        Route::get('/{id}', [PostController::class, 'get']);
        Route::post('/create', [PostController::class, 'store']);
        Route::post('/update/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::post('/upload/{id}', [PostController::class, 'uploadImage']);
        Route::post('/uploadvideo/{id}', [PostController::class, 'uploadVideo']);
        Route::delete('/video/{id}', [PostController::class, 'deleteVideo']);
        Route::post('/{id}/position', [PostController::class, 'changeImagePosition']);
    });

    /*
    | Image routes - Bilder
    */
    Route::group(['prefix' => 'image'], function () {
        Route::post('/update/{id}', [ImageController::class, 'update']);
        Route::delete('/{id}', [ImageController::class, 'destroy']);
    });

    /*
    | Options routes - Optionen
    */

    Route::group(['prefix' => 'option'], function () {
        Route::get('/{option}', [AdminController::class, 'getOption']);
        Route::post('/', [AdminController::class, 'updateOption']);
    });


    /*
    | Misc routes - Verschiedenes
    */

    Route::get('dashboard', [AdminController::class, 'getDashboard']);


    /* 
    | Authentication routes
    */
    /*     Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::options('/login', ['middleware' => 'cors', function () {
            return;
        }]);

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
    }); */
});
