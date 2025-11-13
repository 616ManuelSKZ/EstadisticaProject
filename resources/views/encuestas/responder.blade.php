@extends('layouts.app')

@section('title', 'Responder - ' . $encuesta->titulo)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 animate-fade-in">
        <a href="{{ route('encuestas.index') }}" class="text-purple-600 hover:text-purple-700 font-medium mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Encuestas
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Encabezado de la Encuesta -->
        <div class="gradient-bg px-8 py-10 text-center">
            <h1 class="text-3xl font-bold text-white mb-3">{{ $encuesta->titulo }}</h1>
            @if($encuesta->descripcion)
                <p class="text-purple-100 text-lg">{{ $encuesta->descripcion }}</p>
            @endif
        </div>

        <!-- Formulario -->
        <form action="{{ route('encuestas.guardar-respuesta', $encuesta) }}" method="POST" class="p-8">
            @csrf

            <!-- Nombre del Participante -->
            <div class="mb-8">
                <label for="participante" class="block text-sm font-semibold text-gray-700 mb-3">
                    Tu nombre (Opcional)
                </label>
                <input type="text" 
                       name="participante" 
                       id="participante"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                       placeholder="Deja este campo vacío para responder de forma anónima">
            </div>

            <!-- Opciones -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-4">
                    Selecciona tu respuesta *
                </label>
                
                <div class="space-y-3">
                    @foreach($encuesta->opciones as $opcion)
                        <label class="relative flex items-center p-5 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition-all">
                            <input type="radio" 
                                   name="opcion_id" 
                                   value="{{ $opcion->id }}" 
                                   required
                                   class="w-5 h-5 text-purple-600 focus:ring-purple-500">
                            
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $opcion->color }}"></div>
                                <span class="text-lg font-medium text-gray-900">{{ $opcion->texto }}</span>
                            </div>
                            
                            <!-- Checkmark cuando está seleccionado -->
                            <svg class="hidden absolute right-5 w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </label>
                    @endforeach
                </div>

                @error('opcion_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('encuestas.show', $encuesta) }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    Ver resultados
                </a>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg flex items-center space-x-2">
                    <span>Enviar Respuesta</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
        <div class="flex">
            <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 mb-2">📊 Proyecto Educativo</h3>
                <p class="text-blue-800 mb-2">Esta encuesta forma parte de un proyecto de análisis estadístico y probabilístico.</p>
                <p class="text-blue-700 text-sm">Tu respuesta ayudará a generar datos reales para el estudio de frecuencias, porcentajes y simulaciones de decisión.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Añadir efecto visual al seleccionar una opción
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remover estilos de todas las labels
            document.querySelectorAll('label.relative').forEach(label => {
                label.classList.remove('border-purple-500', 'bg-purple-50');
                label.classList.add('border-gray-200');
                label.querySelector('svg').classList.add('hidden');
            });
            
            // Añadir estilos a la label seleccionada
            const label = this.closest('label');
            label.classList.remove('border-gray-200');
            label.classList.add('border-purple-500', 'bg-purple-50');
            label.querySelector('svg').classList.remove('hidden');
        });
    });
</script>
@endpush
@endsection