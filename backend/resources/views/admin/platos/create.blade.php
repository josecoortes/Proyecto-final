<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Añadir Nuevo Plato') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.platos.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Hamburguesa</label>
                            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required value="{{ old('nombre') }}">
                        </div>

                        <div class="mb-4">
                            <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio (€)</label>
                            <input type="number" step="0.01" name="precio" id="precio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required value="{{ old('precio') }}">
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ingredientes / Descripción</label>
                            <textarea name="descripcion" id="descripcion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="imagen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL de la Imagen (PNG transparente idealmente)</label>
                            <input type="url" name="imagen" id="imagen" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ old('imagen') }}">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.platos.index') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-red-600 font-bold text-white rounded-lg hover:bg-red-700">
                                Guardar Plato
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
