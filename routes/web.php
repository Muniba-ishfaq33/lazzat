<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteRecipeController;
use App\Http\Controllers\GroceryItemController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SavedRecipeController;
use Illuminate\Support\Facades\Route;

// ── Page routes ────────────────────────────────────────────────
Route::get('/',              [PageController::class, 'home'])->name('home');
Route::get('/recipes',       [PageController::class, 'recipes'])->name('recipes');
Route::get('/recipe-detail', [PageController::class, 'recipeDetail'])->name('recipe.detail');
Route::get('/planner',       [PageController::class, 'planner'])->name('planner');
Route::get('/grocery',       [PageController::class, 'grocery'])->name('grocery');
Route::get('/login',         [PageController::class, 'login'])->name('login');
Route::get('/register',      [PageController::class, 'register'])->name('register');

// Dashboard — only accessible when logged in
Route::get('/dashboard', [PageController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

// ── Auth API ───────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');
    Route::post('/logout',   [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/me',        [AuthController::class, 'me'])->name('auth.me');
});

// ── AI chat ────────────────────────────────────────────────────
Route::post('/ai/chat', [AiChatController::class, 'store'])->name('ai.chat');

// ── Data API (protected — must be logged in) ───────────────────
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get   ('/saved-recipes',               [SavedRecipeController::class,    'index']  )->name('saved-recipes.index');
    Route::post  ('/saved-recipes',               [SavedRecipeController::class,    'store']  )->name('saved-recipes.store');
    Route::delete('/saved-recipes/{savedRecipe}', [SavedRecipeController::class,    'destroy'])->name('saved-recipes.destroy');

    Route::get   ('/favorite-recipes',                  [FavoriteRecipeController::class, 'index']  )->name('favorite-recipes.index');
    Route::post  ('/favorite-recipes',                  [FavoriteRecipeController::class, 'store']  )->name('favorite-recipes.store');
    Route::delete('/favorite-recipes/{favoriteRecipe}', [FavoriteRecipeController::class, 'destroy'])->name('favorite-recipes.destroy');

    Route::get   ('/meal-plans',             [MealPlanController::class,    'index']  )->name('meal-plans.index');
    Route::post  ('/meal-plans',             [MealPlanController::class,    'store']  )->name('meal-plans.store');
    Route::delete('/meal-plans/{mealPlan}',  [MealPlanController::class,    'destroy'])->name('meal-plans.destroy');

    Route::get   ('/grocery-items',               [GroceryItemController::class, 'index']  )->name('grocery-items.index');
    Route::post  ('/grocery-items',               [GroceryItemController::class, 'store']  )->name('grocery-items.store');
    Route::patch ('/grocery-items/{groceryItem}', [GroceryItemController::class, 'update'] )->name('grocery-items.update');
    Route::delete('/grocery-items/{groceryItem}', [GroceryItemController::class, 'destroy'])->name('grocery-items.destroy');
});
