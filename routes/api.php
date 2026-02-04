<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PrivacyPolicyController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

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

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/sendPasswordResetLink', [AuthController::class, 'sendPasswordResetLink']);
});

// Privacy Policy (public)
Route::get('/getPrivacyPolicy', [PrivacyPolicyController::class, 'getPrivacyPolicy']);

// Protected routes (JWT authentication required)
Route::middleware(['jwt.auth'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('/getProfile', [AuthController::class, 'getProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::put('/updateProfile', [ProfileController::class, 'updateProfile']);
        Route::put('/updatePassword', [ProfileController::class, 'updatePassword']);
    });

    // Note routes
    Route::prefix('notes')->group(function () {
        // Bulk operations (must be before single note routes)
        Route::post('/bulk', [NoteController::class, 'bulkCreate']);
        Route::put('/bulk', [NoteController::class, 'bulkUpdate']);
        Route::delete('/bulk', [NoteController::class, 'bulkDelete']);

        // Single note operations
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);
        Route::get('/{id}', [NoteController::class, 'show']);
        Route::put('/{id}', [NoteController::class, 'update']);
        Route::delete('/{id}', [NoteController::class, 'destroy']);
        Route::put('/{id}/pin', [NoteController::class, 'togglePin']);
    });
});
