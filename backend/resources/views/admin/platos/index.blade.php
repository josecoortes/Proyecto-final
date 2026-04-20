<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Platos') }}
            </h2>
            <a href="{{ route('admin.platos.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-bold">
                + Añadir Nuevo Plato
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left">Foto</th>
                                    <th class="px-4 py-2 text-left">Nombre</th>
                                    <th class="px-4 py-2 text-left">Precio</th>
                                    <th class="px-4 py-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($platos as $plato)
                                    <tr class="border-t border-gray-200 dark:border-gray-600">
                                        <td class="px-4 py-3">
                                            <img src="{{ $plato->imagen ?? 'https://via.placeholder.com/80?text=Logo' }}" alt="{{ $plato->nombre }}" class="w-16 h-16 object-cover rounded">
                                        </td>
                                        <td class="px-4 py-3 font-bold">{{ $plato->nombre }}</td>
                                        <td class="px-4 py-3 text-yellow-600 font-bold">{{ number_format($plato->precio, 2) }} €</td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('admin.platos.edit', $plato->id) }}" class="text-blue-500 hover:text-blue-700 font-bold mr-3">Editar</a>
                                            <form action="{{ route('admin.platos.destroy', $plato->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿De verdad quieres borrar este plato? ¡Desaparecerá de la carta de los clientes!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-gray-500">Aún no hay ningún plato en la carta. ¡Añade tu primera hamburguesa!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $platos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
