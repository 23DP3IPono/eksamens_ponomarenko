<?php

namespace App\Http\Controllers;

use App\Models\Celojums;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $favorites = $user->favoriteCelojumi()
            ->with('lietotajs')
            ->orderBy('sakuma_datums', 'desc')
            ->get();

        return response()->json($favorites);
    }
    public function store(Request $request, $celojuma_id)
    {
        $celojums = Celojums::find($celojuma_id);
        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }

        $user = $request->user();
        $user->favoriteCelojumi()->syncWithoutDetaching([$celojuma_id]);

        return response()->json(['message' => 'Pievienots iecienītajiem']);
    }

    public function destroy(Request $request, $celojuma_id)
    {
        $user = $request->user();
        $user->favoriteCelojumi()->detach($celojuma_id);

        return response()->json(['message' => 'Noņemts no iecienītajiem']);
    }

    public function check(Request $request, $celojuma_id)
    {
        $user = $request->user();
        $exists = $user->favoriteCelojumi()->where('celojums.celojuma_id', $celojuma_id)->exists();

        return response()->json(['favorited' => $exists]);
    }
}