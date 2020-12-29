<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AuthController;



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
    });

    /*
    | Albums routes - Album
    */
    Route::group(['prefix' => 'album'], function () {
        Route::get('/', [AlbumController::class, 'index']);
        Route::get('/{id}', [AlbumController::class, 'edit']);
        Route::post('/create', [AlbumController::class, 'store']);
    });


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
