<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Añadir Nuevo Empleado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-8">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <div class="mb-8">
                        <h3 class="font-bold text-2xl mb-2">👤 Registrar Trabajador</h3>
                        <p class="text-gray-500 dark:text-gray-400">Rellena los datos para darle acceso al panel de Marina Burguer.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">¡Error!</strong> Revisa los campos obligatorios:
                            <ul class="list-disc pl-5 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.empleados.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Nombre -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Nombre Completo</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Correo Electrónico (Login)</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Rol -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Rol en la Empresa</label>
                            <select name="rol" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <option value="empleado" {{ old('rol') == 'empleado' ? 'selected' : '' }}>Cocina / Empleado (Acceso solo a Platos)</option>
                                <option value="cajero" {{ old('rol') == 'cajero' ? 'selected' : '' }}>Cajero (Solo cobros en local)</option>
                                <option value="repartidor" {{ old('rol') == 'repartidor' ? 'selected' : '' }}>Repartidor (Solo cobros a domicilio)</option>
                                <option value="gestor" {{ old('rol') == 'gestor' ? 'selected' : '' }}>Gestor (Acceso solo al Dashboard Financiero)</option>
                                <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador (Acceso Total)</option>
                            </select>
                        </div>

                        <!-- Contraseña -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Contraseña</label>
                                <input type="password" name="password" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Repetir Contraseña</label>
                                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="pt-6 flex justify-between items-center border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.empleados.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium">Cancelar</a>
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md transition">Guardar Empleado</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
