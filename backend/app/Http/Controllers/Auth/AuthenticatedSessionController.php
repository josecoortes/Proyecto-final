<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse
    {
        // Redirigir al usuario al login del frontend
        return redirect()->to(config('app.frontend_url', 'http://localhost:4200') . '/login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Redirección dinámica basada en el rol (Forzamos la ruta, sin usar intended para evitar el error de 403)
        $rol = auth()->user()->rol;

        if ($rol === 'empleado') {
            return redirect(route('admin.platos.index', absolute: false));
        } elseif ($rol === 'repartidor' || $rol === 'cajero') {
            return redirect(route('admin.pedidos.index', absolute: false));
        }

        // Por defecto, admin y gestor van al Dashboard
        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
