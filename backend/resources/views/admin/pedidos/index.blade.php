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
                            @elseif($userRol === 'cajero')  Panel de Caja
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
                        $pedidosPendientes = $pedidos->where('estado', 'pendiente');
                        $pedidosPreparando = $pedidos->where('estado', 'preparando');
                        $pedidosListos = $pedidos->where('estado', 'listo');
                        $pedidosReparto = $pedidos->where('estado', 'en_reparto');
                        $pedidosCompletados = $pedidos->whereIn('estado', ['entregado', 'cancelado']);
                    @endphp

                    @if(count($pedidos) === 0)
                        <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-lg">Aún no hay pedidos registrados en el sistema.</p>
                        </div>
                    @else
                        
                        <!-- TAB NAVIGATION -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs" id="tabs-nav">
                                <!-- Pendientes -->
                                <button onclick="openTab('tab-pendientes', this)" class="tab-btn active border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm">
                                    Nuevos
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">{{ count($pedidosPendientes) }}</span>
                                </button>

                                <!-- Preparando -->
                                <button onclick="openTab('tab-preparando', this)" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm">
                                    En Cocina
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">{{ count($pedidosPreparando) }}</span>
                                </button>

                                <!-- Listos -->
                                <button onclick="openTab('tab-listos', this)" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm">
                                    Listos
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">{{ count($pedidosListos) }}</span>
                                </button>

                                <!-- En Reparto -->
                                <button onclick="openTab('tab-reparto', this)" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm">
                                    En Reparto
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">{{ count($pedidosReparto) }}</span>
                                </button>

                                <!-- Historial -->
                                <button onclick="openTab('tab-completados', this)" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap flex py-4 px-1 border-b-2 font-medium text-sm">
                                    Historial
                                </button>
                            </nav>
                        </div>

                        <!-- TAB CONTENT: PENDIENTES -->
                        <div id="tab-pendientes" class="tab-content">
                            @if(count($pedidosPendientes) === 0)
                                <p class="text-gray-500 dark:text-gray-400 my-8 text-center">No hay pedidos nuevos.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($pedidosPendientes as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- TAB CONTENT: PREPARANDO -->
                        <div id="tab-preparando" class="tab-content hidden">
                            @if(count($pedidosPreparando) === 0)
                                <p class="text-gray-500 dark:text-gray-400 my-8 text-center">No hay pedidos en cocina.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($pedidosPreparando as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- TAB CONTENT: LISTOS -->
                        <div id="tab-listos" class="tab-content hidden">
                            @if(count($pedidosListos) === 0)
                                <p class="text-gray-500 dark:text-gray-400 my-8 text-center">No hay pedidos listos esperando.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($pedidosListos as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- TAB CONTENT: EN REPARTO -->
                        <div id="tab-reparto" class="tab-content hidden">
                            @if(count($pedidosReparto) === 0)
                                <p class="text-gray-500 dark:text-gray-400 my-8 text-center">No hay pedidos en reparto.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($pedidosReparto as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- TAB CONTENT: COMPLETADOS / HISTORIAL -->
                        <div id="tab-completados" class="tab-content hidden">
                            @if(count($pedidosCompletados) === 0)
                                <p class="text-gray-500 dark:text-gray-400 my-8 text-center">No hay pedidos finalizados hoy.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($pedidosCompletados as $pedido)
                                        @include('admin.pedidos.partials.card', ['pedido' => $pedido])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- TABS LOGIC -->
    <script>
        function openTab(tabId, element) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active classes from all buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400', 'active');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Show selected tab content
            document.getElementById(tabId).classList.remove('hidden');

            // Add active classes to clicked button
            element.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            element.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400', 'active');
        }

        // Auto-select tab logic based on user role (optional UX enhancement)
        document.addEventListener('DOMContentLoaded', () => {
            const userRol = "{{ $userRol }}";
            if (userRol === 'repartidor') {
                // If there are 'en_reparto' orders, show them first
                const btnReparto = document.querySelector('button[onclick*="tab-reparto"]');
                if (btnReparto && parseInt(btnReparto.querySelector('span').innerText) > 0) {
                    btnReparto.click();
                } else {
                    const btnListos = document.querySelector('button[onclick*="tab-listos"]');
                    if (btnListos) btnListos.click();
                }
            } else if (userRol === 'cajero') {
                const btnNuevos = document.querySelector('button[onclick*="tab-pendientes"]');
                if (btnNuevos) btnNuevos.click();
            }
        });
    </script>
</x-app-layout>
