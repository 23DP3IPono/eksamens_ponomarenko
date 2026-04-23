<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'uzvards' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|max:100',
        ], [
            'name.required' => 'Vārds ir obligāts',
            'uzvards.required' => 'Uzvārds ir obligāts',
            'email.required' => 'E-pasts ir obligāts',
            'email.email' => 'E-pasta formāts nav pareizs',
            'email.unique' => 'Šāds e-pasts jau ir reģistrēts',
            'password.required' => 'Parole ir obligāta',
            'password.min' => 'Parolei jābūt vismaz 6 simbolus garai',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'uzvards' => $data['uzvards'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'loma' => 'Registrets',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Nepareizs e-pasts vai parole',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * POST /api/logout (requires auth)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Atslēgts']);
    }

    /**
     * GET /api/me (requires auth)
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}