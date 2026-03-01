<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\Request;

class PlatoController extends Controller
{
    // 1. LISTAR TODOS (GET /api/platos)
    public function index(Request $request)
    {
        $query = Plato::with('categoria');

        if ($request->has('search')) {
            $termino = $request->input('search');
            $query->where('nombre', 'LIKE', "%{$termino}%")
                ->orWhere('descripcion', 'LIKE', "%{$termino}%");
        }

        $orden = $request->input('order', 'id');
        $direccion = $request->input('dir', 'asc');
        $query->orderBy($orden, $direccion);

        $limite = $request->input('limit', 10);
        $paginados = $query->paginate($limite);

        // MAGIA: through() limpia el array de 'data' pero mantiene intacta la paginación (current_page, total, etc)
        $paginados->through(function ($plato) {
            return [
                'id' => $plato->id,
                'nombre' => $plato->nombre,
                'descripcion' => $plato->descripcion,
                'precio' => $plato->precio,
                'imagen' => $plato->imagen,
                'categoria' => $plato->categoria ? $plato->categoria->nombre : 'Sin categoría',
            ];
        });

        return response()->json($paginados);
    }
    // 2. VER UN SOLO PLATO (GET /api/platos/{id})
    public function show($id)
    {
        $plato = Plato::with(['categoria', 'comentarios.user'])->find($id);

        if (!$plato) {
            return response()->json(['error' => 'Plato no encontrado'], 404);
        }

        // Construimos un JSON limpio solo con lo que Angular necesita
        $respuestaLimpia = [
            'id' => $plato->id, // El ID del plato sí es útil para Angular (para el carrito)
            'nombre' => $plato->nombre,
            'descripcion' => $plato->descripcion,
            'precio' => $plato->precio,
            'imagen' => $plato->imagen,
            'categoria' => $plato->categoria ? $plato->categoria->nombre : 'Sin categoría', // Solo mandamos el texto de la categoría

            // Transformamos (map) los comentarios para ocultar los datos del usuario
            'comentarios' => $plato->comentarios->map(function ($comentario) {
                return [
                    'autor' => $comentario->user->name, // Solo el nombre, ocultamos el email y el ID
                    'valoracion' => $comentario->valoracion,
                    'texto' => $comentario->contenido,
                    'fecha' => $comentario->fecha // Usamos tu fecha, ignoramos los created_at
                ];
            })
        ];

        return response()->json($respuestaLimpia);
    }

    // 3. CREAR PLATO (POST /api/platos)
    public function store(Request $request)
    {
        // Validaciones automáticas (Laravel devuelve error 400 si falta algo)
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|string'
        ]);

        $plato = Plato::create($validated);

        return response()->json([
            'mensaje' => 'Plato creado correctamente',
            'plato' => $plato
        ], 201);
    }

    // 4. ACTUALIZAR PLATO (PUT /api/platos/{id})
    public function update(Request $request, $id)
    {
        $plato = Plato::find($id);

        if (!$plato) {
            return response()->json(['error' => 'Plato no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'precio' => 'sometimes|required|numeric|min:0',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|string'
        ]);

        $plato->update($validated);

        return response()->json([
            'mensaje' => 'Plato actualizado correctamente',
            'plato' => $plato
        ]);
    }

    // 5. BORRAR PLATO (DELETE /api/platos/{id})
    public function destroy($id)
    {
        $plato = Plato::find($id);

        if (!$plato) {
            return response()->json(['error' => 'Plato no encontrado'], 404);
        }

        $plato->delete();

        return response()->json(['mensaje' => 'Plato eliminado correctamente']);
    }
}
