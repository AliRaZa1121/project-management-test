<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimesheetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->get('user', [AuthController::class, 'user'])->name('api.user');
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout'])->name('api.logout');

Route::group(['middleware' => 'auth:sanctum'], function () {

    /****************** Projects ******************/
    Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects');
    Route::post('/projects', [ProjectController::class, 'createProject'])->name('api.create.project');
    Route::get('/projects/{id}', [ProjectController::class, 'showProject'])->name('api.show.project');
    Route::put('/projects/{id}', [ProjectController::class, 'updateProject'])->name('api.update.project');
    Route::delete('/projects/{id}', [ProjectController::class, 'deleteProject'])->name('api.delete.project');

    /****************** Timesheets ******************/
    Route::get('/timesheets', [TimesheetController::class, 'getProjectTimesheets'])->name('api.get.project.timesheets');
    Route::post('/timesheets', [TimesheetController::class, 'createProjectTimesheet'])->name('api.create.project.timesheet');
    Route::get('/timesheets/{timesheetId}', [TimesheetController::class, 'showProjectTimesheet'])->name('api.show.project.timesheet');
    Route::put('/timesheets/{timesheetId}', [TimesheetController::class, 'updateProjectTimesheet'])->name('api.update.project.timesheet');
    Route::delete('/timesheets/{timesheetId}', [TimesheetController::class, 'deleteProjectTimesheet'])->name('api.delete.project.timesheet');


    /****************** Attributes ******************/
    Route::get('/attributes', [AttributeController::class, 'getAttributes'])->name('api.get.attributes');
    Route::post('/attributes', [AttributeController::class, 'createAttribute'])->name('api.create.attribute');
    Route::post('/projects/{id}/attributes', [AttributeController::class, 'setProjectAttributes'])->name('api.set.project.attributes');
    Route::get('/projects/{id}/attributes', [AttributeController::class, 'getProjectAttributes'])->name('api.get.project.attributes');


});
