<?php

namespace App\Http\Controllers;

use App\Models\Vieta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VietaController extends Controller
{
    /**
     * GET /api/vietas
     * List all places with optional search.
     */
    public function index(Request $request)
    {
        $query = Vieta::query();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nosaukums', 'like', "%{$search}%")
                  ->orWhere('adrese', 'like', "%{$search}%");
        }

        return response()->json($query->orderBy('nosaukums')->get());
    }

    /**
     * POST /api/vietas
     * Create a new place.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nosaukums' => 'required|string|max:100',
            'adrese' => 'nullable|string|max:150',
            'koordinatas' => 'nullable|string|max:100',
            'tips' => 'nullable|string|max:50',
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