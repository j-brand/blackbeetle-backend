<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\SubscriptionController;

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
        Route::get('/generate-title-images', [AlbumController::class, 'regenerateTitleImages']);
        Route::get('/generate-images', [AlbumController::class, 'regenerateImages']);
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
        Route::get('/generate-title-images', [StoryController::class, 'regenerateTitleImages']);
        Route::get('/{id}', [StoryController::class, 'get']);
        Route::post('/create', [StoryController::class, 'store']);
        Route::post('/update/{id}', [StoryController::class, 'update']);
        Route::delete('/{id}', [StoryController::class, 'destroy']);
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
        Route::post('/upload-video/{id}', [PostController::class, 'uploadVideo']);
        Route::delete('/video/{id}', [PostController::class, 'deleteVideo']);
        Route::post('/{id}/position', [PostController::class, 'changeImagePosition']);
        Route::post('/{id}/swap', [PostController::class, 'swapImagePosition']);
    });

    /*
    | Comment routes - Kommentare
    */
    Route::group(['prefix' => 'comment'], function () {
        Route::get('/{id}', [CommentController::class, 'get']);
        Route::get('/byPost/{id}', [CommentController::class, 'getByPost']);
        //Route::post('/create', [CommentController::class, 'store']);
        Route::post('/update/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    /*
    | Subscriber routes - Abonnenten
    */
    Route::group(['prefix' => 'subscriber'], function () {
        Route::get('/', [SubscriberController::class, 'index']);
        Route::get('/send-verification/{id}', [SubscriberController::class, 'sendVerification']);
        Route::delete('/{id}', [SubscriberController::class, 'destroy']);
        /*         Route::get('/byPost/{id}', [CommentController::class, 'getByPost']);
        Route::post('/update/{id}', [CommentController::class, 'update']);
        */
    });

    /*
    | Subscription routes - Abonnements
    */
    Route::group(['prefix' => 'subscriptions'], function () {
        /*         Route::get('/{id}', [CommentController::class, 'get']);
        Route::get('/byPost/{id}', [CommentController::class, 'getByPost']);
        Route::post('/update/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']); */
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
    | Notification routes - Benachrichtigungen/Subscriptions
    */

    Route::post('/notify', [NotificationController::class, 'notify']);

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
