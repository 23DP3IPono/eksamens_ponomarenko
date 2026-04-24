<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * POST /api/messages
     * Save a contact form message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vards' => 'required|string|max:50',
            'epasts' => 'required|email|max:100',
            'zina' => 'required|string|min:5|max:2000',
        ], [
            'vards.required' => 'Vārds ir obligāts',
            'epasts.required' => 'E-pasts ir obligāts',
            'epasts.email' => 'E-pasta formāts nav pareizs',
            'zina.required' => 'Ziņa ir obligāta',
            'zina.min' => 'Ziņai jābūt vismaz 5 simboli',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = Message::create($validator->validated());

        return response()->json([
            'message' => 'Ziņa nosūtīta',
            'data' => $message,
        ], 201);
    }
}