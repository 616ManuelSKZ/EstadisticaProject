<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function opciones(): HasMany
    {
        return $this->hasMany(Opcion::class);
    }

    /**
     * Obtener todas las respuestas de la encuesta
     */
    public function respuestas()
    {
        return Respuesta::whereIn('opcion_id', $this->opciones->pluck('id'));
    }

    /**
     * Calcular estadísticas de la encuesta
     */
    public function estadisticas(): array
    {
        $opciones = $this->opciones()->with('respuestas')->get();
        $totalRespuestas = $opciones->sum(fn($o) => $o->respuestas->count());

        $datos = $opciones->map(function ($opcion) use ($totalRespuestas) {
            $frecuencia = $opcion->respuestas->count();
            $porcentaje = $totalRespuestas > 0 ? ($frecuencia / $totalRespuestas) * 100 : 0;

            return [
                'opcion' => $opcion->texto,
                'color' => $opcion->color,
                'frecuencia' => $frecuencia,
                'porcentaje' => round($porcentaje, 2),
            ];
        });

        // Calcular moda (opción más frecuente)
        $moda = $datos->sortByDesc('frecuencia')->first();

        // Calcular media
        $media = $totalRespuestas > 0 ? $totalRespuestas / $opciones->count() : 0;

        // Calcular rango
        $frecuencias = $datos->pluck('frecuencia');
        $rango = $frecuencias->max() - $frecuencias->min();

        return [
            'datos' => $datos->values()->all(),
            'total_respuestas' => $totalRespuestas,
            'moda' => $moda ? $moda['opcion'] : 'N/A',
            'moda_frecuencia' => $moda ? $moda['frecuencia'] : 0,
            'media' => round($media, 2),
            'rango' => $rango,
        ];
    }

    /**
     * Simular probabilidad de que un nuevo participante elija una opción
     */
    public function simularDecision(): ?string
    {
        $estadisticas = $this->estadisticas();
        
        if ($estadisticas['total_respuestas'] === 0) {
            // Si no hay respuestas, elegir aleatoriamente
            $opcionAleatoria = $this->opciones->random();
            return $opcionAleatoria ? $opcionAleatoria->texto : null;
        }

        // Usar probabilidades basadas en porcentajes actuales
        $random = mt_rand(1, 10000) / 100; // Número aleatorio entre 0 y 100
        $acumulado = 0;

        foreach ($estadisticas['datos'] as $dato) {
            $acumulado += $dato['porcentaje'];
            if ($random <= $acumulado) {
                return $dato['opcion'];
            }
        }

        // Fallback
        return $estadisticas['datos'][0]['opcion'] ?? null;
    }

    /**
     * Generar múltiples simulaciones
     */
    public function simularMultiplesDecisiones(int $cantidad = 100): array
    {
        $resultados = [];
        
        for ($i = 0; $i < $cantidad; $i++) {
            $decision = $this->simularDecision();
            if ($decision) {
                $resultados[$decision] = ($resultados[$decision] ?? 0) + 1;
            }
        }

        return $resultados;
    }
}