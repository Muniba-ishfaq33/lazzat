<?php

namespace App\Http\Controllers;

use App\Models\FavoriteRecipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteRecipeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            FavoriteRecipe::where('user_id', Auth::id())->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mealdb_id' => ['required', 'string', 'max:40'],
            'name'      => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:1000'],
            'category'  => ['nullable', 'string', 'max:120'],
            'area'      => ['nullable', 'string', 'max:120'],
            'payload'   => ['nullable', 'array'],
        ]);

        $data['user_id'] = Auth::id();

        $favorite = FavoriteRecipe::updateOrCreate(
            ['user_id' => Auth::id(), 'mealdb_id' => $data['mealdb_id']],
            $data
        );

        return response()->json($favorite, 201);
    }

    public function destroy(FavoriteRecipe $favoriteRecipe): JsonResponse
    {
        if ($favoriteRecipe->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $favoriteRecipe->delete();
        return response()->json(['deleted' => true]);
    }
}
