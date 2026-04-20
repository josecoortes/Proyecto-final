<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\Request;

class AdminPlatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // He puesto paginación de 10 en 10 aquí porque si subimos 50 hamburguesas 
        // a la vez, se nos va a petar la vista de la tabla.
        $platos = Plato::paginate(10);
        return view('admin.platos.index', compact('platos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.platos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0', // Evitamos que alguien ponga precios negativos por error
            // De momento guardamos la URL directa de la imagen para no complicarnos con el Storage de Laravel
            // Ya si nos da tiempo lo cambiamos a subida de archivos real.
            'imagen' => 'nullable|url', 
            'categoria_id' => 'nullable|integer'
        ]);

        Plato::create($validatedData);

        return redirect()->route('admin.platos.index')->with('success', '¡El plato ha sido añadido al menú con éxito!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plato $plato)
    {
        return view('admin.platos.edit', compact('plato'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plato $plato)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|url', 
            'categoria_id' => 'nullable|integer'
        ]);

        // Pillamos los datos validados y sobreescribimos el plato en la base de datos
        // Laravel lo hace automático con este update().
        $plato->update($validatedData);

        return redirect()->route('admin.platos.index')->with('success', '¡El plato se ha actualizado correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plato $plato)
    {
        // Ojo: Si borramos el plato, desaparece en tiempo real del frontal de Angular.
        // Quizás deberíamos meterle "Soft Deletes" en el futuro si el profesor lo pide, 
        // pero por ahora lo fulminamos directamente de la tabla con delete().
        $plato->delete();

        return redirect()->route('admin.platos.index')->with('success', 'El plato ha sido retirado del menú de forma permanente.');
    }
}
