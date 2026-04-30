<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Mostrar la lista de empleados.
     */
    public function index()
    {
        // Solo mostramos en el panel a los usuarios que SON empleados, no a los clientes que piden hamburguesas.
        $empleados = User::whereIn('rol', ['admin', 'gestor', 'empleado', 'repartidor', 'cajero'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
        return view('admin.empleados.index', compact('empleados'));
    }

    /**
     * Mostrar formulario para crear un nuevo empleado.
     */
    public function create()
    {
        return view('admin.empleados.create');
    }

    /**
     * Guardar el nuevo empleado en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'in:admin,gestor,empleado,repartidor,cajero'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * Mostrar formulario para editar empleado.
     */
    public function edit(User $empleado)
    {
        return view('admin.empleados.edit', compact('empleado'));
    }

    /**
     * Actualizar datos del empleado.
     */
    public function update(Request $request, User $empleado)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$empleado->id],
            'rol' => ['required', 'in:admin,gestor,empleado,repartidor,cajero'],
        ];

        // Solo validamos la contraseña si se rellenó el campo
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $empleado->name = $request->name;
        $empleado->email = $request->email;
        $empleado->rol = $request->rol;

        if ($request->filled('password')) {
            $empleado->password = Hash::make($request->password);
        }

        $empleado->save();

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Eliminar empleado.
     */
    public function destroy(User $empleado)
    {
        // Evitar que el admin supremo se borre a sí mismo
        if (auth()->id() === $empleado->id) {
            return redirect()->route('admin.empleados.index')->with('error', 'No puedes eliminar tu propia cuenta principal.');
        }

        $empleado->delete();

        return redirect()->route('admin.empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
