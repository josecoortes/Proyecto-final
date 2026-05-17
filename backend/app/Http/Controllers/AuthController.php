<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    // --- REGISTRO DE USUARIO ---
    public function register(Request $request)
    {
        // 1. Validamos que los datos vengan bien
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase() // Al menos una mayúscula y una minúscula
                    ->numbers()   // Al menos un número
                    ->symbols()   // Al menos un carácter especial
            ],
        ]);

        try {
            // 2. Creamos el usuario en la base de datos
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'], // El modelo User lo cifra automáticamente con su cast 'hashed'
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno en el servidor.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
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

        $response = [
            'message' => '¡Hola de nuevo ' . $user->name . '!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];

        // 4. Si el usuario es staff, generamos un token de auto-login cifrado para el panel admin
        $rolesStaff = ['admin', 'gestor', 'empleado', 'repartidor', 'cajero'];
        if (in_array($user->rol, $rolesStaff)) {
            // Cifrar: "user_id|timestamp_expiry" con la APP_KEY de Laravel
            $expiry = now()->addMinutes(5)->timestamp;
            $payload = $user->id . '|' . $expiry;
            $response['admin_token'] = \Illuminate\Support\Facades\Crypt::encryptString($payload);
        }

        return response()->json($response);
    }
}