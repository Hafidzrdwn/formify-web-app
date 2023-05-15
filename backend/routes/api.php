<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth/login', 'login');
        Route::post('/auth/logout', 'logout')->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(FormController::class)->group(function () {

            Route::post('/forms', 'createForm');
            Route::get('/forms', 'getForms');
            Route::get('/forms/{slug}', 'detailForm');
        });
        Route::controller(QuestionController::class)->group(function () {
            Route::post('/forms/{slug}/questions', 'addQuestion');
            Route::delete('/forms/{slug}/questions/{id}', 'removeQuestion');
        });

        Route::post('/forms/{slug}/responses', [ResponseController::class, 'submit']);
        Route::get('/forms/{slug}/responses', [ResponseController::class, 'getAll']);
    });
});
