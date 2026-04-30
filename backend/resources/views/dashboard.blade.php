<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Analítico') }}
        </h2>
    </x-slot>

    <!-- Importar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Cabecera y Navegación de Tiempo -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                        <div>
                            <h3 class="font-bold text-2xl mb-2">📊 Panel Financiero: Marina Burguer</h3>
                            <p class="text-gray-500 dark:text-gray-400">Análisis de rendimiento, beneficios netos y desglose de pedidos.</p>
                        </div>
                        
                        <!-- Selector de Tiempo Interactivo -->
                        <div class="mt-4 md:mt-0 flex items-center bg-gray-50 dark:bg-gray-700/50 rounded-full p-1 border border-gray-200 dark:border-gray-700">
                            <a href="{{ $linkMesAnterior }}" class="px-4 py-2 rounded-full hover:bg-white dark:hover:bg-gray-600 transition shadow-sm text-gray-600 dark:text-gray-300">
                                ←
                            </a>
                            <span class="px-6 font-bold text-lg text-gray-800 dark:text-gray-100 w-48 text-center uppercase tracking-wide">
                                {{ $nombreMesActual }}
                            </span>
                            <a href="{{ $linkMesSiguiente }}" class="px-4 py-2 rounded-full hover:bg-white dark:hover:bg-gray-600 transition shadow-sm text-gray-600 dark:text-gray-300">
                                →
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contenido Principal: Grid de 2 Columnas -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 antialiased">
                        
                        <!-- Columna Izquierda: Tarjetas de Métricas (5/12) -->
                        <div class="md:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-5 h-fit">
                            
                            <!-- Beneficio Neto -->
                            <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/40 dark:to-green-900/20 rounded-2xl border border-green-200 dark:border-green-800 shadow-sm flex flex-col justify-center">
                                <h4 class="font-bold text-sm text-green-800 dark:text-green-400 uppercase tracking-wider">Beneficio Neto</h4>
                                <p class="text-3xl mt-2 font-black text-gray-900 dark:text-white">{{ number_format($beneficioActual, 2) }} €</p>
                                <p class="text-sm mt-1 font-semibold {{ $crecimiento >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $crecimiento >= 0 ? '↑' : '↓' }} {{ number_format(abs($crecimiento), 1) }}% vs Mes
                                </p>
                            </div>

                            <!-- Ingresos Hoy -->
                            <div class="p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/40 dark:to-yellow-900/20 rounded-2xl border border-yellow-200 dark:border-yellow-800 shadow-sm flex flex-col justify-center">
                                <h4 class="font-bold text-sm text-yellow-800 dark:text-yellow-400 uppercase tracking-wider">Ingresos Hoy</h4>
                                <p class="text-3xl mt-2 font-black text-gray-900 dark:text-white">{{ number_format($ingresosHoy ?? 0, 2) }} €</p>
                                <p class="text-sm mt-1 font-medium text-yellow-600 dark:text-yellow-400">En las últimas 24h</p>
                            </div>

                            <!-- Ingresos Brutos -->
                            <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-800 shadow-sm flex flex-col justify-center">
                                <h4 class="font-bold text-sm text-blue-800 dark:text-blue-400 uppercase tracking-wider">Ingresos Mes</h4>
                                <p class="text-3xl mt-2 font-black text-gray-900 dark:text-white">{{ number_format($ingresosActual, 2) }} €</p>
                                <p class="text-sm mt-1 font-medium text-blue-600 dark:text-blue-400">Total facturado</p>
                            </div>

                            <!-- Gastos -->
                            <div class="relative p-6 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/40 dark:to-red-900/20 rounded-2xl border border-red-200 dark:border-red-800 shadow-sm flex flex-col justify-center group">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-sm text-red-800 dark:text-red-400 uppercase tracking-wider">Gastos Mes</h4>
                                    <!-- Botón para añadir Gasto (abre modal) -->
                                    <button onclick="document.getElementById('modalGasto').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-lg shadow-sm transition-transform hover:scale-110" title="Añadir Gasto">
                                        +
                                    </button>
                                </div>
                                <p class="text-3xl mt-2 font-black text-gray-900 dark:text-white">{{ number_format($gastosActual, 2) }} €</p>
                                <p class="text-sm mt-1 font-medium text-red-600 dark:text-red-400">Haz clic en + para añadir</p>
                            </div>
                        </div>

                        <!-- Columna Derecha: Gráficos (7/12) -->
                        <div class="md:col-span-7 grid grid-cols-1 gap-6">
                            
                            <!-- Gráfico de Balance -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 flex flex-col h-full justify-between">
                                <h4 class="font-bold text-sm mb-2 text-gray-800 dark:text-gray-200">Balance Mensual</h4>
                                <div class="relative w-full flex-grow" style="min-height: 200px;">
                                    <canvas id="balanceChart"></canvas>
                                </div>
                            </div>

                            <!-- Gráfico de Tipos de Entrega -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 flex flex-col items-center h-full justify-between">
                                <h4 class="font-bold text-sm mb-2 text-gray-800 dark:text-gray-200 w-full text-left">Canales de Entrega</h4>
                                <div class="relative w-full flex-grow flex justify-center" style="min-height: 200px;">
                                    <canvas id="deliveryChart"></canvas>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Flotante para Añadir Gasto -->
    <div id="modalGasto" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">💸 Añadir Nuevo Gasto</h3>
            <form action="{{ route('dashboard.gasto.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Concepto</label>
                    <input type="text" name="concepto" required placeholder="Ej: Pago a proveedores de carne" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto (€)</label>
                    <input type="number" step="0.01" name="monto" required placeholder="0.00" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('modalGasto').classList.add('hidden')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de Inicialización de Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración general para modo oscuro/claro
            Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563';
            Chart.defaults.font.family = "'Inter', 'Nunito', sans-serif";

            // 1. Gráfico de Balance (Barras)
            const ctxBalance = document.getElementById('balanceChart').getContext('2d');
            new Chart(ctxBalance, {
                type: 'bar',
                data: {
                    labels: ['Mes Anterior', 'Mes Actual'],
                    datasets: [
                        {
                            label: 'Ingresos (€)',
                            data: [{{ $ingresosAnterior }}, {{ $ingresosActual }}],
                            backgroundColor: 'rgba(59, 130, 246, 0.8)', // Azul
                            borderRadius: 6
                        },
                        {
                            label: 'Gastos (€)',
                            data: [{{ $gastosAnterior }}, {{ $gastosActual }}],
                            backgroundColor: 'rgba(239, 68, 68, 0.8)', // Rojo
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // Desactiva animaciones para evitar advertencias de requestAnimationFrame
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Gráfico de Entregas (Donut)
            const ctxDelivery = document.getElementById('deliveryChart').getContext('2d');
            new Chart(ctxDelivery, {
                type: 'doughnut',
                data: {
                    labels: ['A Domicilio', 'Recogida Local'],
                    datasets: [{
                        data: [{{ $ventasDomicilio }}, {{ $ventasRecogida }}],
                        backgroundColor: [
                            'rgba(245, 158, 11, 0.8)', // Naranja (Domicilio)
                            'rgba(16, 185, 129, 0.8)'  // Verde (Recogida)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animation: false, // Desactiva animaciones
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
