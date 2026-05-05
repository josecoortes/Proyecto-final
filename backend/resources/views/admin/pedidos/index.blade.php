<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Logística y Control de Pedidos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <h3 class="font-bold text-2xl mb-2">
                            @if($userRol === 'admin')  Panel de Logística General
                            @elseif($userRol === 'cajero')  Panel de Caja (Recogida)
                            @else Panel de Repartidores @endif
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">Revisa la dirección de entrega de cada cliente y cambia el estado de la comanda.</p>
                    </div>

                    @if($userRol === 'admin')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-5 flex items-center justify-between shadow-sm">
                            <div>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Pedidos Exitosos</p>
                                <p class="text-4xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $completadosHoy }}</p>
                                <p class="text-xs text-green-500 mt-1">Pedidos entregados y pagados hoy</p>
                            </div>
                            <div class="p-4 bg-green-100 dark:bg-green-800 rounded-full">
                                <span class="text-3xl">✅</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @php
                        $pedidosDomicilio = $pedidos->where('metodo_entrega', 'domicilio');
                        $pedidosRecogida = $pedidos->where('metodo_entrega', 'recoger');
                    @endphp

                    @if(count($pedidos) === 0)
                        <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg">Aún no hay pedidos registrados en el sistema.</p>
                        </div>
                    @else

                        @if($userRol === 'admin' || $userRol === 'repartidor')
                            @if($userRol === 'admin') <h4 class="text-xl font-bold mb-4 mt-8 text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2">🛵 Pedidos a Domicilio</h4> @endif
                            
                            @if(count($pedidosDomicilio) === 0 && $userRol !== 'admin')
                                <p class="text-gray-500 dark:text-gray-400 my-4">No hay pedidos a domicilio pendientes.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                                    @foreach ($pedidosDomicilio as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        @if($userRol === 'admin' || $userRol === 'cajero')
                            @if($userRol === 'admin') <h4 class="text-xl font-bold mb-4 mt-8 text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2">🏪 Pedidos para Recoger</h4> @endif
                            
                            @if(count($pedidosRecogida) === 0 && $userRol !== 'admin')
                                <p class="text-gray-500 dark:text-gray-400 my-4">No hay pedidos para recoger pendientes.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                                    @foreach ($pedidosRecogida as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        @endif

                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
