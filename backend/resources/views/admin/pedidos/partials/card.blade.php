<div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl border {{ $pedido->estado === 'entregado' ? 'border-green-300 dark:border-green-800' : 'border-gray-200 dark:border-gray-600' }} shadow-sm overflow-hidden flex flex-col h-full">
    <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 flex justify-between items-center">
        <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
            Pedido #{{ $pedido->id }}
        </div>
        <div>
            @if($pedido->metodo_entrega === 'domicilio')
                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-bold uppercase tracking-wider">A Domicilio</span>
            @else
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold uppercase tracking-wider">Recogida</span>
            @endif
        </div>
    </div>

    <!-- Datos Cliente y Logística -->
    <div class="p-4 flex-grow flex flex-col space-y-3">
        
        <div class="flex items-center text-sm">
            <span class="text-gray-500 dark:text-gray-400 w-20 font-medium">Cliente:</span>
            <span class="font-bold text-gray-500 dark:text-gray-400">{{ $pedido->user->name }}</span>
        </div>
        
        <div class="flex items-center text-sm">
            <span class="text-gray-500 dark:text-gray-400 w-20 font-medium">Teléfono:</span>
            <span class="font-bold text-gray-500 dark:text-gray-400">{{ $pedido->user->telefono ?? 'No indicado' }}</span>
        </div>
        
        <div class="flex items-start text-sm">
            <span class="text-gray-500 dark:text-gray-400 w-20 font-medium mt-0.5">Dirección:</span>
            <div class="flex-1 text-gray-500 dark:text-gray-400">
                @if($pedido->metodo_entrega === 'domicilio')
                    @if($pedido->direccion_empresa)
                        {{ $pedido->direccion_empresa }}
                    @else
                        <span class="text-red-500">Dirección no proporcionada</span>
                    @endif
                @else
                    <span class="text-gray-500 dark:text-gray-400 italic">Viene a recogerlo al local</span>
                @endif
            </div>
        </div>

        <div class="flex items-center text-sm">
            <span class="text-gray-500 dark:text-gray-400 w-20 font-medium">Fecha:</span>
            <span class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($pedido->hora)->format('H:i') }}</span>
        </div>

        <!-- Lista de Platos -->
        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
            <h4 class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2">Resumen de Comanda</h4>
            <ul class="text-sm space-y-1">
                @foreach($pedido->platos as $plato)
                    <li class="flex justify-between text-green-600">
                        <span>{{ $plato->pivot->cantidad }}x {{ $plato->nombre }}</span>
                        <span class="font-medium">{{ number_format($plato->precio * $plato->pivot->cantidad, 2) }}€</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!--Cambio de Estado y Pagos -->
    <div class="p-4 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600">
        <form action="{{ route('admin.pedidos.estado', $pedido->id) }}" method="POST" class="flex flex-col space-y-2 mb-4">
            @csrf
            @method('PATCH')
            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado Logístico</label>
            <div class="flex space-x-2">
                <select name="estado" class="flex-grow rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                    
                    <option value="preparando" {{ $pedido->estado === 'preparando' ? 'selected' : '' }} @if($userRol === 'repartidor' && $pedido->estado !== 'preparando') disabled hidden @endif>🍳 Preparando (Cocina)</option>
                    
                    <option value="en_reparto" {{ $pedido->estado === 'en_reparto' ? 'selected' : '' }}>🛵 En Reparto</option>
                    
                    <option value="entregado" {{ $pedido->estado === 'entregado' ? 'selected' : '' }}>✅ Entregado</option>
                    
                    <option value="cancelado" {{ $pedido->estado === 'cancelado' ? 'selected' : '' }} @if($userRol === 'repartidor' && $pedido->estado !== 'cancelado') disabled hidden @endif>❌ Cancelado</option>
                </select>
                <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-bold shadow transition">
                    Actualizar
                </button>
            </div>
        </form>

        <!-- Estado de Pago -->
        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
            @if($pedido->estado_pago === 'pagado')
                <div class="flex items-center text-green-600 dark:text-green-400 font-bold">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    PAGADO ({{ ucfirst($pedido->metodo_pago) }})
                </div>
            @else
                @if($pedido->metodo_pago === 'tarjeta')
                    <div class="flex items-center text-blue-600 dark:text-blue-400 font-bold">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        PAGADO (TARJETA)
                    </div>
                @else
                    <div class="flex items-center text-red-600 dark:text-red-400 font-bold">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        PENDIENTE COBRO
                    </div>
                    <form action="{{ route('admin.pedidos.pago', $pedido->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded shadow text-sm font-bold flex items-center transition">
                            Cobrar 💶
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
