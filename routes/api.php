<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FilesController;
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

Route::get('activity', [ActivityController::class, 'index']);
Route::get("activity/{id}", [ActivityController::class, 'show']);
Route::get('files', [FilesController::class, 'index']);

Route::post('activity', [ActivityController::class, 'store']);
Route::post('activity/search', [ActivityController::class, 'search']);
Route::post('activity/sort', [ActivityController::class, 'sort']);
Route::post('files', [FilesController::class, 'store']);
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::delete("activity/{id}", [ActivityController::class, 'delete']);
Route::delete("files", [FilesController::class, 'delete']);

Route::put('activity/edit/{id}', [ActivityController::class, 'update']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
