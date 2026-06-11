<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealPlanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            MealPlan::where('user_id', Auth::id())
                ->orderBy('day_index')
                ->orderBy('meal_index')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'day_index'  => ['required', 'integer', 'between:0,6'],
            'meal_index' => ['required', 'integer', 'between:0,2'],
            'mealdb_id'  => ['required', 'string', 'max:40'],
            'name'       => ['required', 'string', 'max:255'],
            'thumbnail'  => ['nullable', 'string', 'max:1000'],
            'payload'    => ['nullable', 'array'],
        ]);

        $data['user_id'] = Auth::id();

        $mealPlan = MealPlan::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'day_index'  => $data['day_index'],
                'meal_index' => $data['meal_index'],
            ],
            $data
        );

        return response()->json($mealPlan, 201);
    }

    public function destroy(MealPlan $mealPlan): JsonResponse
    {
        if ($mealPlan->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mealPlan->delete();
        return response()->json(['deleted' => true]);
    }
}
