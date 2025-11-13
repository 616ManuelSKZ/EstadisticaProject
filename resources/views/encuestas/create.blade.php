@extends('layouts.app')

@section('title', 'Crear Encuesta')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 animate-fade-in">
        <a href="{{ route('encuestas.index') }}" class="text-purple-600 hover:text-purple-700 font-medium mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Encuestas
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-2">📝 Crear Nueva Encuesta</h1>
        <p class="text-gray-600">Diseña una encuesta para recolectar datos estadísticos</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form action="{{ route('encuestas.store') }}" method="POST" id="encuestaForm">
            @csrf

            <!-- Título -->
            <div class="mb-6">
                <label for="titulo" class="block text-sm font-semibold text-gray-700 mb-2">
                    Título de la Encuesta *
                </label>
                <input type="text" 
                       name="titulo" 
                       id="titulo" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                       placeholder="Ej: ¿Cómo prefieren pasar el fin de semana?"
                       value="{{ old('titulo') }}">
                @error('titulo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                    Descripción (Opcional)
                </label>
                <textarea name="descripcion" 
                          id="descripcion" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                          placeholder="Añade contexto o instrucciones para los participantes">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opciones -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        Opciones de Respuesta * (mínimo 2)
                    </label>
                    <button type="button" 
                            onclick="agregarOpcion()"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-600 transition flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Agregar Opción</span>
                    </button>
                </div>

                <div id="opcionesContainer" class="space-y-3">
                    <!-- Opciones iniciales -->
                    <div class="opcion-item flex items-center space-x-3">
                        <input type="text" 
                               name="opciones[]" 
                               required
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                               placeholder="Opción 1">
                        <input type="color" 
                               name="colores[]" 
                               value="#3B82F6"
                               class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                        <button type="button" 
                                onclick="eliminarOpcion(this)"
                                class="bg-red-500 text-white px-3 py-3 rounded-lg hover:bg-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="opcion-item flex items-center space-x-3">
                        <input type="text" 
                               name="opciones[]" 
                               required
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                               placeholder="Opción 2">
                        <input type="color" 
                               name="colores[]" 
                               value="#10B981"
                               class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                        <button type="button" 
                                onclick="eliminarOpcion(this)"
                                class="bg-red-500 text-white px-3 py-3 rounded-lg hover:bg-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @error('opciones')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('encuestas.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg">
                    Crear Encuesta
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let contadorOpciones = 2;
    const colores = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];

    function agregarOpcion() {
        contadorOpciones++;
        const color = colores[contadorOpciones % colores.length];
        
        const container = document.getElementById('opcionesContainer');
        const opcionDiv = document.createElement('div');
        opcionDiv.className = 'opcion-item flex items-center space-x-3 animate-fade-in';
        opcionDiv.innerHTML = `
            <input type="text" 
                   name="opciones[]" 
                   required
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                   placeholder="Opción ${contadorOpciones}">
            <input type="color" 
                   name="colores[]" 
                   value="${color}"
                   class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
            <button type="button" 
                    onclick="eliminarOpcion(this)"
                    class="bg-red-500 text-white px-3 py-3 rounded-lg hover:bg-red-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        container.appendChild(opcionDiv);
    }

    function eliminarOpcion(button) {
        const container = document.getElementById('opcionesContainer');
        const opciones = container.querySelectorAll('.opcion-item');
        
        if (opciones.length <= 2) {
            alert('Debe haber al menos 2 opciones');
            return;
        }
        
        button.closest('.opcion-item').remove();
    }
</script>
@endpush
@endsection