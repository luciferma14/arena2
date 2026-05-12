<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        // Respuesta JSON para tests/API
        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user'  => $user,
                'token' => $token,
            ], 201);
        }

        // Respuesta web: login y redirigir
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Respuesta JSON para tests/API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Las credenciales son incorrectas.',
                    'errors'  => [
                        'email' => ['Las credenciales son incorrectas.'],
                    ],
                ], 422);
            }

            // Respuesta web
            return back()->withErrors([
                'email' => 'Las credenciales son incorrectas.',
            ])->onlyInput('email');
        }

        // Respuesta JSON para tests/API
        if ($request->expectsJson()) {
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user'  => Auth::user(),
                'token' => $token,
            ], 200);
        }

        // Respuesta web
        $request->session()->regenerate();

        if (Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Respuesta JSON para tests/API
        if ($request->expectsJson()) {
            // Revocar token actual si existe
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            return response()->json([
                'message' => 'Sesión cerrada correctamente',
            ], 200);
        }

        // Respuesta web
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Obtener usuario autenticado
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
