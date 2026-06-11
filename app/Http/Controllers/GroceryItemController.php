<?php

namespace App\Http\Controllers;

use App\Models\GroceryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroceryItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            GroceryItem::where('user_id', Auth::id())
                ->orderBy('checked')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'measure' => ['nullable', 'string', 'max:120'],
            'source'  => ['nullable', 'string', 'max:255'],
            'checked' => ['boolean'],
        ]);

        $data['user_id'] = Auth::id();

        return response()->json(GroceryItem::create($data), 201);
    }

    public function update(Request $request, GroceryItem $groceryItem): JsonResponse
    {
        if ($groceryItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'    => ['sometimes', 'string', 'max:255'],
            'measure' => ['nullable', 'string', 'max:120'],
            'source'  => ['nullable', 'string', 'max:255'],
            'checked' => ['sometimes', 'boolean'],
        ]);

        $groceryItem->update($data);
        return response()->json($groceryItem);
    }

    public function destroy(GroceryItem $groceryItem): JsonResponse
    {
        if ($groceryItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $groceryItem->delete();
        return response()->json(['deleted' => true]);
    }
}
