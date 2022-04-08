<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\NotesController;
use App\Http\Controllers\API\UploadsController;

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

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'notes', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [NotesController::class, 'index']);
    Route::post('add', [NotesController::class, 'add']);
    Route::get('edit/{id}', [NotesController::class, 'edit']);
    Route::post('update/{id}', [NotesController::class, 'update']);
    Route::delete('delete/{id}', [NotesController::class, 'delete']);
    Route::post('shared/users/list', [NotesController::class, 'sharedNoteUserList']);
    Route::get('shared/list', [NotesController::class, 'sharedNotesList']);
});

Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
  Route::get('list', [UserController::class, 'list']);
});

Route::group(['prefix' => 'uploads', 'middleware' => 'auth:sanctum'], function () {
  Route::post('file', [UploadsController::class, 'fileUpload']);
});

