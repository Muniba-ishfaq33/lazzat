<?php

namespace App\Http\Controllers;

use App\Models\SavedRecipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedRecipeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            SavedRecipe::where('user_id', Auth::id())->latest()->get()
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

        $data['user_id'] = Auth::id(); // always use logged-in user

        $recipe = SavedRecipe::updateOrCreate(
            ['user_id' => Auth::id(), 'mealdb_id' => $data['mealdb_id']],
            $data
        );

        return response()->json($recipe, 201);
    }

    public function destroy(SavedRecipe $savedRecipe): JsonResponse
    {
        // Only allow deleting own recipes
        if ($savedRecipe->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $savedRecipe->delete();
        return response()->json(['deleted' => true]);
    }
}
