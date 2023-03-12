<?php

namespace App\Http\Controllers;

use App\Models\shelves;
use Illuminate\Http\Request;

class ShelfController extends Controller
{
    public function info(Request $request)
    {
        if (auth()->user()) {
            return response()->json([
                'total_shelves' => shelves::count(),
                'total_quantity' => shelves::sum('quantity'),
            ], 200);
        }
    }
    // sırasıyla fonksiyonlar yazılacak ve routes/api.php dosyasına eklenip kullanılacak
    // get_shelf/get_shelves/create_shelf/update_shelf/delete_shelf/search_shelf

    public function get_shelf(Request $request)
    {
        if (auth()->user()) {
            $shelf = shelves::where('id', $request->id)->first();
            if ($shelf) {
                return response()->json([
                    'response' => $shelf,
                ], 200);
            } else {
                return response()->json([
                    'response' => 'Shelf not found!',
                ], 404);
            }
        }
    }

    public function get_shelves(Request $request)
    {
        if (auth()->user()) {
            $shelves = shelves::all();
            if ($shelves) {
                return response()->json([
                    'response' => $shelves,
                ], 200);
            } else {
                return response()->json([
                    'response' => 'Shelves not found!',
                ], 404);
            }
        }
    }

    public function create_shelf(Request $request)
    {
        if (auth()->user()) {
            $shelf = shelves::create([
                'ingredients' => $request->ingredients,
                'quantity' => $request->quantity,
                'number' => $request->number,
            ]);
            if ($shelf) {
                return response()->json([
                    'response' => 'Shelf created successfully!',
                ], 200);
            } else {
                return response()->json([
                    'response' => 'Shelf not created!',
                ], 404);
            }
        }
    }

    public function update_shelf(Request $request)
    {
        if (auth()->user()) {
            $shelf = shelves::where('number', $request->number)->first();
            if ($shelf) {
                $shelf->ingredients = $request->ingredients;
                $shelf->quantity = $request->quantity;
                $shelf->number = $request->number;
                $shelf->save();
                return response()->json([
                    'message' => 'Shelf updated successfully!',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Shelf not found!',
                ], 404);
            }
        }
    }

    public function delete_shelf(Request $request)
    {
        if (auth()->user()) {
            $shelf = shelves::where('number', $request->number)->first();
            if ($shelf) {
                $shelf->delete();
                return response()->json([
                    'response' => 'Shelf deleted successfully!',
                ], 200);
            } else {
                return response()->json([
                    'response' => 'Shelf not found!',
                ], 404);
            }
        }
    }

    public function search_shelf(Request $request)
    {
        if (auth()->user()) {
            if ($request->type == "all") {
                $shelf = shelves::Where('ingredients', 'like', '%' . $request->value . '%')
                    ->orWhere('quantity', 'like', '%' . $request->value . '%')
                    ->orWhere('number', 'like', '%' . $request->value . '%')
                    ->orderBy('number', 'asc')
                    ->get();
            } else {
                $shelf = shelves::where($request->type, 'like', '%' . $request->value . '%')->get();
            }
            if ($shelf) {
                return response()->json([
                    'response' => $shelf,
                ], 200);
            } else {
                return response()->json([
                    'response' => 'Shelf not found!',
                ], 404);
            }
        } else {
            return response()->json([
                'response' => 'You are not authorized!',
            ], 401);
        }
    }
}