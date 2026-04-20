<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl mb-4">🍔 Panel de Control: Marina Burguer</h3>
                    <p class="mb-4">Has iniciado sesión correctamente como Administrador. Desde aquí podrás gestionar los Platos, Categorías y los Pedidos recibidos desde la Web App (Angular).</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                        <div class="p-4 bg-red-100 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                            <h4 class="font-bold text-xl text-red-800 dark:text-red-400">Platos Activos</h4>
                            <p class="text-3xl mt-2 font-black dark:text-white">12</p>
                        </div>
                        <div class="p-4 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <h4 class="font-bold text-xl text-yellow-800 dark:text-yellow-400">Pedidos Hoy</h4>
                            <p class="text-3xl mt-2 font-black dark:text-white">5</p>
                        </div>
                        <div class="p-4 bg-green-100 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                            <h4 class="font-bold text-xl text-green-800 dark:text-green-400">Ingresos</h4>
                            <p class="text-3xl mt-2 font-black dark:text-white">€125.50</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
