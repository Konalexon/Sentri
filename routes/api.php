<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Current user
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'avatar_url' => $user->avatar_url,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
        ]);
    });
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/statistics', [TicketController::class, 'statistics'])->name('tickets.statistics');
        Route::get('/agents', [TicketController::class, 'agents'])->name('tickets.agents');

        Route::prefix('{ticket}')->group(function () {
            Route::get('/', [TicketController::class, 'show'])->name('tickets.show');
            Route::patch('/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
            Route::post('/assign', [TicketController::class, 'assignAgent'])->name('tickets.assign');

            // Messages within a ticket
            Route::get('/messages', [MessageController::class, 'index'])->name('tickets.messages.index');
            Route::post('/messages', [MessageController::class, 'store'])->name('tickets.messages.store');
        });
    });

    // Mark message as read
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

    // Tags (for autocomplete and management)
    Route::apiResource('tags', \App\Http\Controllers\Api\TagController::class);
});

// API Health check
Route::get('/health', fn() => response()->json([
    'status' => 'ok',
    'timestamp' => now()->toIso8601String(),
    'version' => config('app.version', '1.0.0'),
]));
