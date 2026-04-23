<?php

namespace App\Http\Controllers;

use App\Models\Rezervacija;
use App\Models\Celojums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RezervacijaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'celojuma_id' => 'required|exists:celojums,celojuma_id',
            'tips' => 'required|string|in:Aviobilete,Viesnīca,Cits',
            'pakalpojuma_nosaukums' => 'required|string|max:100',
            'cena' => 'required|numeric|min:0',
        ], [
            'celojuma_id.required' => 'Ceļojums ir obligāts',
            'tips.required' => 'Tips ir obligāts',
            'tips.in' => 'Tipam jābūt vienam no: Aviobilete, Viesnīca, Cits',
            'pakalpojuma_nosaukums.required' => 'Pakalpojuma nosaukums ir obligāts',
            'cena.required' => 'Cena ir obligāta',
            'cena.min' => 'Cena nevar būt negatīva',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $celojums = Celojums::find($data['celojuma_id']);
        if ($celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību pievienot rezervāciju šim ceļojumam'], 403);
        }

        $rezervacija = Rezervacija::create($data);

        return response()->json($rezervacija, 201);
    }

    public function update(Request $request, $id)
    {
        $rezervacija = Rezervacija::find($id);
        if (!$rezervacija) {
            return response()->json(['message' => 'Rezervācija nav atrasta'], 404);
        }

        $celojums = Celojums::find($rezervacija->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību rediģēt šo rezervāciju'], 403);
        }

        $validator = Validator::make($request->all(), [
            'tips' => 'sometimes|required|string|in:Aviobilete,Viesnīca,Cits',
            'pakalpojuma_nosaukums' => 'sometimes|required|string|max:100',
            'cena' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rezervacija->update($validator->validated());

        return response()->json($rezervacija);
    }

    public function destroy(Request $request, $id)
    {
        $rezervacija = Rezervacija::find($id);
        if (!$rezervacija) {
            return response()->json(['message' => 'Rezervācija nav atrasta'], 404);
        }

        $celojums = Celojums::find($rezervacija->celojuma_id);
        if (!$celojums || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību dzēst šo rezervāciju'], 403);
        }

        $rezervacija->delete();

        return response()->json(['message' => 'Rezervācija dzēsta']);
    }
}