<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\StudentsPageController;
use App\Http\Controllers\Web\TeachersPageController;
use App\Http\Controllers\Web\SubjectsPageController;
use App\Http\Controllers\ClientApi\SessionController;
use App\Http\Controllers\ClientApi\CoursesController;
use App\Http\Controllers\ClientApi\TeachersController;
use App\Http\Controllers\ClientApi\StudentsController;
use App\Http\Controllers\ClientApi\SubjectsController;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::get('/', fn () => redirect('/dashboard'));
    Route::get('/dashboard', DashboardController::class);

    Route::get('/students', [StudentsPageController::class, 'index']);
    Route::post('/students', [StudentsPageController::class, 'store']);
    Route::delete('/students/{id}', [StudentsPageController::class, 'destroy']);

    Route::get('/teachers', [TeachersPageController::class, 'index']);
    Route::post('/teachers', [TeachersPageController::class, 'store']);
    Route::delete('/teachers/{id}', [TeachersPageController::class, 'destroy']);

    Route::get('/subjects', [SubjectsPageController::class, 'index']);
    Route::post('/subjects', [SubjectsPageController::class, 'store']);
    Route::delete('/subjects/{id}', [SubjectsPageController::class, 'destroy']);
});

Route::prefix('client-api')->middleware('auth')->group(function (): void {
    Route::get('/session', [SessionController::class, 'status']);

    Route::get('/courses', [CoursesController::class, 'index']);
    Route::post('/courses', [CoursesController::class, 'store']);
    Route::get('/courses/{id}', [CoursesController::class, 'show']);
    Route::put('/courses/{id}', [CoursesController::class, 'update']);
    Route::delete('/courses/{id}', [CoursesController::class, 'destroy']);

    Route::get('/teachers', [TeachersController::class, 'index']);
    Route::post('/teachers', [TeachersController::class, 'store']);
    Route::get('/teachers/{id}', [TeachersController::class, 'show']);
    Route::put('/teachers/{id}', [TeachersController::class, 'update']);
    Route::delete('/teachers/{id}', [TeachersController::class, 'destroy']);
    Route::post('/teachers/{id}/assign', [TeachersController::class, 'assign']);
    Route::post('/teachers/{id}/unassign', [TeachersController::class, 'unassign']);

    Route::get('/students', [StudentsController::class, 'index']);
    Route::post('/students', [StudentsController::class, 'store']);
    Route::get('/students/{id}', [StudentsController::class, 'show']);
    Route::put('/students/{id}', [StudentsController::class, 'update']);
    Route::delete('/students/{id}', [StudentsController::class, 'destroy']);
    Route::post('/students/{id}/enroll', [StudentsController::class, 'enroll']);

    Route::get('/subjects', [SubjectsController::class, 'index']);
    Route::post('/subjects', [SubjectsController::class, 'store']);
    Route::get('/subjects/{id}', [SubjectsController::class, 'show']);
    Route::put('/subjects/{id}', [SubjectsController::class, 'update']);
    Route::delete('/subjects/{id}', [SubjectsController::class, 'destroy']);
});
