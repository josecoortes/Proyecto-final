<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Empleados y Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-2xl">👥 Empleados Activos</h3>
                            <p class="text-gray-500 dark:text-gray-400">Controla quién tiene acceso a cada área de la aplicación.</p>
                        </div>
                        <a href="{{ route('admin.empleados.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-sm">
                            + Añadir Empleado
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tabla de Empleados -->
                    <div class="overflow-x-auto bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                    <th class="p-4 font-bold">Nombre</th>
                                    <th class="p-4 font-bold">Email</th>
                                    <th class="p-4 font-bold">Rol</th>
                                    <th class="p-4 font-bold text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empleados as $empleado)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-700 transition">
                                    <td class="p-4 font-medium">{{ $empleado->name }}</td>
                                    <td class="p-4 text-gray-500 dark:text-gray-400">{{ $empleado->email }}</td>
                                    <td class="p-4">
                                        @if($empleado->rol === 'admin')
                                            <span class="px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 rounded-full text-xs font-bold uppercase tracking-wider">Administrador</span>
                                        @elseif($empleado->rol === 'gestor')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 rounded-full text-xs font-bold uppercase tracking-wider">Gestor Finanzas</span>
                                        @elseif($empleado->rol === 'cajero')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 rounded-full text-xs font-bold uppercase tracking-wider">Cajero</span>
                                        @elseif($empleado->rol === 'repartidor')
                                            <span class="px-3 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 rounded-full text-xs font-bold uppercase tracking-wider">Repartidor</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 rounded-full text-xs font-bold uppercase tracking-wider">Empleado Platos</span>
                                        @endif
                                    </td>
                                    <!-- Botones de Acción (Estilo nativo para asegurar separación) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center items-center" style="gap: 1rem;">
                                            <a href="{{ route('admin.empleados.edit', $empleado->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-bold bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                                ✏️ Editar
                                            </a>
                                            <form action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que quieres despedir a este empleado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-bold bg-red-50 dark:bg-red-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                                    🗑️ Despedir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
