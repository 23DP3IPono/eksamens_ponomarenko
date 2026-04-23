<?php

namespace App\Http\Controllers;

use App\Models\DienasPunkts;
use App\Models\Celojums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DienasPunktsController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'celojuma_id' => 'required|exists:celojums,celojuma_id',
            'vieta_id' => 'required|exists:vieta,vieta_id',
            'datums' => 'required|date',
            'apraksts' => 'nullable|string|max:200',
        ], [
            'celojuma_id.required' => 'Ceļojums ir obligāts',
            'vieta_id.required' => 'Vieta ir obligāta',
            'vieta_id.exists' => 'Izvēlētā vieta nepastāv',
            'datums.required' => 'Datums ir obligāts',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $celojums = Celojums::find($data['celojuma_id']);
        if ($celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību pievienot punktu šim ceļojumam'], 403);
        }

        $punkts = DienasPunkts::create($data);
        $punkts->load('vieta');

        return response()->json($punkts, 201);
    }

    public function update(Request $request, $id)
    {
        $punkts = DienasPunkts::find($id);
        if (!$punkts) {
            return response()->json(['message' => 'Punkts nav atrasts'], 404);
        }

        $celojums = Celojums::find($punkts->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību rediģēt šo punktu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'vieta_id' => 'sometimes|required|exists:vieta,vieta_id',
            'datums' => 'sometimes|required|date',
            'apraksts' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $punkts->update($validator->validated());
        $punkts->load('vieta');

        return response()->json($punkts);
    }

    public function destroy(Request $request, $id)
    {
        $punkts = DienasPunkts::find($id);
        if (!$punkts) {
            return response()->json(['message' => 'Punkts nav atrasts'], 404);
        }

        $celojums = Celojums::find($punkts->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību dzēst šo punktu'], 403);
        }

        $punkts->delete();

        return response()->json(['message' => 'Punkts dzēsts']);
    }
}