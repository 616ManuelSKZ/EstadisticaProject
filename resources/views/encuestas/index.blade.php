@extends('layouts.app')

@section('title', 'Lista de Encuestas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Encabezado -->
    <div class="mb-8 animate-fade-in">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">📊 Panel de Encuestas</h1>
        <p class="text-gray-600">Gestiona, analiza y visualiza tus encuestas estadísticas</p>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-wrap gap-4 mb-8">
        <a href="{{ route('encuestas.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Crear Nueva Encuesta</span>
        </a>
        
        <form action="{{ route('encuestas.generar-ejemplo') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition shadow-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                <span>Generar Datos de Ejemplo</span>
            </button>
        </form>
    </div>

    <!-- Lista de Encuestas -->
    @if($encuestas->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center animate-fade-in">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay encuestas aún</h3>
            <p class="text-gray-500 mb-6">Comienza creando tu primera encuesta o genera datos de ejemplo</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('encuestas.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Crear Encuesta
                </a>
                <form action="{{ route('encuestas.generar-ejemplo') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                        Datos de Ejemplo
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($encuestas as $encuesta)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover animate-fade-in">
                    <!-- Estado -->
                    <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $encuesta->titulo }}</h3>
                                @if($encuesta->descripcion)
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($encuesta->descripcion, 80) }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $encuesta->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $encuesta->activa ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>{{ $encuesta->opciones_count }} opciones</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $encuesta->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="px-6 py-4 bg-white border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{{ route('encuestas.show', $encuesta) }}" class="flex-1 bg-purple-600 text-white text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                                Ver Resultados
                            </a>
                            <a href="{{ route('encuestas.responder', $encuesta) }}" class="flex-1 bg-green-500 text-white text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-600 transition">
                                Responder
                            </a>
                            <form action="{{ route('encuestas.destroy', $encuesta) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta encuesta?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection