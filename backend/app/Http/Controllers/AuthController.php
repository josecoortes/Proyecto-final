<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // --- REGISTRO DE USUARIO ---
    public function register(Request $request)
    {
        // 1. Validamos que los datos vengan bien
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 2. Creamos el usuario en la base de datos
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // ¡Importante! Encriptar contraseña
            'rol' => 'cliente', // Por defecto todos son clientes
        ]);

        // 3. Creamos un token de acceso (es como su llave digital)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Devolvemos la respuesta al Frontend
        return response()->json([
            'message' => '¡Usuario registrado con éxito!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // --- LOGIN DE USUARIO ---
    public function login(Request $request)
    {
        // 1. Intentamos loguear con el email y contraseña
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login inválido. Revisa tus credenciales.'
            ], 401);
        }

        // 2. Si es correcto, buscamos al usuario
        $user = User::where('email', $request['email'])->firstOrFail();

        // 3. Le damos una llave (token) nueva
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '¡Hola de nuevo ' . $user->name . '!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}