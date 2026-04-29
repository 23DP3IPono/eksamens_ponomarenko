<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Celojums;
use App\Models\Vieta;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * GET /api/admin/stats
     * Overview counts.
     */
    public function stats()
    {
        return response()->json([
            'users_total' => User::count(),
            'users_registered' => User::where('loma', 'Registrets')->count(),
            'users_admins' => User::where('loma', 'Admins')->count(),
            'trips_total' => Celojums::count(),
            'places_total' => Vieta::count(),
            'messages_total' => Message::count(),
        ]);
    }

    /**
     * GET /api/admin/users
     */
    public function users()
    {
        return response()->json(
            User::withCount('celojumi')
                ->orderBy('id')
                ->get()
        );
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Lietotājs nav atrasts'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Nevar dzēst pats sevi'], 400);
        }

        if ($user->loma === 'Admins') {
            return response()->json(['message' => 'Nevar dzēst citu administratoru'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Lietotājs dzēsts']);
    }

    /**
     * GET /api/admin/trips
     */
    public function trips()
    {
        return response()->json(
            Celojums::with('lietotajs')
                ->orderBy('celojuma_id', 'desc')
                ->get()
        );
    }

    /**
     * DELETE /api/admin/trips/{id}
     */
    public function deleteTrip($id)
    {
        $celojums = Celojums::find($id);
        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }
        $celojums->delete();
        return response()->json(['message' => 'Ceļojums dzēsts']);
    }

    /**
     * GET /api/admin/messages
     */
    public function messages()
    {
        return response()->json(
            Message::orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * DELETE /api/admin/messages/{id}
     */
    public function deleteMessage($id)
    {
        $message = Message::find($id);
        if (!$message) {
            return response()->json(['message' => 'Ziņa nav atrasta'], 404);
        }
        $message->delete();
        return response()->json(['message' => 'Ziņa dzēsta']);
    }
}