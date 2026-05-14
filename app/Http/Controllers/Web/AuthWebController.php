<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Forzamos Accept JSON para obtener respuesta estructurada del AuthController
        // y evitar que redirija internamente
        $request->headers->set('Accept', 'application/json');

        try {
            $response = app(AuthController::class)->login($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('email');
        }

        $status = $response->getStatusCode();
        $data   = $response->getData(true);

        if ($status !== 200) {
            $msg = $data['errors']['email'][0] ?? ($data['message'] ?? 'Credenciales incorrectas.');
            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        // Auth::attempt ya ejecutó login en la sesión dentro del AuthController
        $request->session()->regenerate();
        session(['api_token' => $data['token']]);

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.index');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        try {
            $response = app(AuthController::class)->register($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $status = $response->getStatusCode();
        $data   = $response->getData(true);

        if ($status !== 201) {
            return back()->withErrors($data['errors'] ?? ['email' => 'Error al registrar.'])->withInput();
        }

        // Para JSON el AuthController no llama Auth::login, lo hacemos aquí
        Auth::loginUsingId($data['user']['id']);
        $request->session()->regenerate();
        session(['api_token' => $data['token']]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(Request $request)
    {
        // Llamada en memoria al controlador API (sin HTTP interno)
        $response = app(\App\Http\Controllers\EntradaController::class)->index($request);
        $entradas = $response->getData(true)['data'] ?? [];

        // Solo las 5 últimas para el dashboard
        $entradas = array_slice($entradas, 0, 5);

        return view('dashboard.index', compact('entradas'));
    }
}
