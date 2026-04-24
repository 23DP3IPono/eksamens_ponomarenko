<?php

namespace App\Http\Controllers;

use App\Models\Celojums;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * GET /api/favorites
     * List the current user's favorite trips.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $favorites = $user->favoriteCelojumi()
            ->with('lietotajs')
            ->orderBy('sakuma_datums', 'desc')
            ->get();

        return response()->json($favorites);
    }

    /**
     * POST /api/favorites/{celojuma_id}
     * Mark a trip as favorite.
     */
    public function store(Request $request, $celojuma_id)
    {
        $celojums = Celojums::find($celojuma_id);
        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }

        $user = $request->user();

        // syncWithoutDetaching adds the pivot row without removing others
        $user->favoriteCelojumi()->syncWithoutDetaching([$celojuma_id]);

        return response()->json(['message' => 'Pievienots iecienītajiem']);
    }

    /**
     * DELETE /api/favorites/{celojuma_id}
     * Remove a trip from favorites.
     */
    public function destroy(Request $request, $celojuma_id)
    {
        $user = $request->user();
        $user->favoriteCelojumi()->detach($celojuma_id);

        return response()->json(['message' => 'Noņemts no iecienītajiem']);
    }

    /**
     * GET /api/favorites/check/{celojuma_id}
     * Check if a specific trip is in the current user's favorites.
     * Returns { favorited: true|false }
     */
    public function check(Request $request, $celojuma_id)
    {
        $user = $request->user();
        $exists = $user->favoriteCelojumi()->where('celojums.celojuma_id', $celojuma_id)->exists();

        return response()->json(['favorited' => $exists]);
    }
}