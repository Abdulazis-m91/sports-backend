<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SportsController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

// Public Routes - Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Routes - Sports
Route::get('/leagues', [SportsController::class, 'leagues']);
Route::get('/leagues/{leagueId}/teams', [SportsController::class, 'teams']);
Route::get('/teams/{teamId}', [SportsController::class, 'teamDetail']);
Route::get('/teams/{teamId}/previous-matches', [SportsController::class, 'previousMatches']);
Route::get('/leagues/{leagueId}/standings', [SportsController::class, 'standings']);
Route::get('/teams/search', [SportsController::class, 'searchTeams']);

// Protected Routes - Auth Required
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{teamId}', [FavoriteController::class, 'destroy']);
});