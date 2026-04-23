<?php

namespace App\Http\Controllers;

use App\Models\Izdevums;
use App\Models\Celojums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IzdevumsController extends Controller
{
    /**
     * POST /api/izdevumi
     * Create an expense for a trip.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'celojuma_id' => 'required|exists:celojums,celojuma_id',
            'summa' => 'required|numeric|min:0',
            'datums' => 'required|date',
            'kategorija' => 'required|string|max:50',
        ], [
            'celojuma_id.required' => 'Ceļojums ir obligāts',
            'summa.required' => 'Summa ir obligāta',
            'summa.min' => 'Summa nevar būt negatīva',
            'datums.required' => 'Datums ir obligāts',
            'kategorija.required' => 'Kategorija ir obligāta',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Ownership check — only the trip owner can add expenses to it
        $celojums = Celojums::find($data['celojuma_id']);
        if ($celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību pievienot izdevumu šim ceļojumam'], 403);
        }

        $izdevums = Izdevums::create($data);

        return response()->json($izdevums, 201);
    }

    /**
     * PUT /api/izdevumi/{id}
     */
    public function update(Request $request, $id)
    {
        $izdevums = Izdevums::find($id);
        if (!$izdevums) {
            return response()->json(['message' => 'Izdevums nav atrasts'], 404);
        }

        // Ownership check
        $celojums = Celojums::find($izdevums->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību rediģēt šo izdevumu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'summa' => 'sometimes|required|numeric|min:0',
            'datums' => 'sometimes|required|date',
            'kategorija' => 'sometimes|required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $izdevums->update($validator->validated());

        return response()->json($izdevums);
    }

    /**
     * DELETE /api/izdevumi/{id}
     */
    public function destroy(Request $request, $id)
    {
        $izdevums = Izdevums::find($id);
        if (!$izdevums) {
            return response()->json(['message' => 'Izdevums nav atrasts'], 404);
        }

        $celojums = Celojums::find($izdevums->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību dzēst šo izdevumu'], 403);
        }

        $izdevums->delete();

        return response()->json(['message' => 'Izdevums dzēsts']);
    }
}