<?php

namespace App\Http\Controllers;

use App\Models\Vieta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VietaController extends Controller
{
    public function index(Request $request)
    {
        $query = Vieta::query();

        if ($request->filled('valsts')) {
            $query->where('valsts', $request->query('valsts'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nosaukums', 'like', "%{$search}%")
                  ->orWhere('adrese', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('nosaukums')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nosaukums' => 'required|string|max:100',
            'adrese' => 'nullable|string|max:150',
            'valsts' => 'nullable|string|max:100',
            'tips' => 'nullable|string|max:50',
            'koordinatas' => 'nullable|string|max:100',
        ], [
            'nosaukums.required' => 'Nosaukums ir obligāts',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vieta = Vieta::create($validator->validated());

        return response()->json($vieta, 201);
    }
}