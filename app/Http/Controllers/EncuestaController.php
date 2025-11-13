<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\Opcion;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    /**
     * Listar todas las encuestas
     */
    public function index()
    {
        $encuestas = Encuesta::withCount('opciones')->latest()->get();
        return view('encuestas.index', compact('encuestas'));
    }

    /**
     * Mostrar formulario de crear encuesta
     */
    public function create()
    {
        return view('encuestas.create');
    }

    /**
     * Guardar nueva encuesta
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'opciones' => 'required|array|min:2',
            'opciones.*' => 'required|string|max:255',
            'colores' => 'array',
            'colores.*' => 'string',
        ]);

        DB::transaction(function () use ($request) {
            $encuesta = Encuesta::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'activa' => true,
            ]);

            $colores = $request->colores ?? [];
            
            foreach ($request->opciones as $index => $textoOpcion) {
                Opcion::create([
                    'encuesta_id' => $encuesta->id,
                    'texto' => $textoOpcion,
                    'color' => $colores[$index] ?? $this->generarColorAleatorio(),
                ]);
            }
        });

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta creada exitosamente');
    }

    /**
     * Ver detalles y estadísticas de una encuesta
     */
    public function show(Encuesta $encuesta)
    {
        $estadisticas = $encuesta->estadisticas();
        $simulacion = $encuesta->simularMultiplesDecisiones(1000);
        
        return view('encuestas.show', compact('encuesta', 'estadisticas', 'simulacion'));
    }

    /**
     * Mostrar formulario para responder encuesta
     */
    public function responder(Encuesta $encuesta)
    {
        if (!$encuesta->activa) {
            return redirect()->route('encuestas.index')
                ->with('error', 'Esta encuesta ya no está activa');
        }

        return view('encuestas.responder', compact('encuesta'));
    }

    /**
     * Guardar respuesta
     */
    public function guardarRespuesta(Request $request, Encuesta $encuesta)
    {
        $request->validate([
            'opcion_id' => 'required|exists:opciones,id',
            'participante' => 'nullable|string|max:255',
        ]);

        // Verificar que la opción pertenece a la encuesta
        $opcion = Opcion::where('id', $request->opcion_id)
            ->where('encuesta_id', $encuesta->id)
            ->firstOrFail();

        Respuesta::create([
            'opcion_id' => $opcion->id,
            'participante' => $request->participante ?? 'Anónimo',
            'fecha_respuesta' => now(),
        ]);

        return redirect()->route('encuestas.show', $encuesta)
            ->with('success', '¡Gracias por tu respuesta!');
    }

    /**
     * Exportar resultados a CSV
     */
    public function exportarCSV(Encuesta $encuesta)
    {
        $estadisticas = $encuesta->estadisticas();
        
        $filename = 'encuesta_' . $encuesta->id . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($encuesta, $estadisticas) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8 (para Excel)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, ['Encuesta', $encuesta->titulo]);
            fputcsv($file, ['Fecha', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Total de Respuestas', $estadisticas['total_respuestas']]);
            fputcsv($file, []);
            
            // Estadísticas generales
            fputcsv($file, ['Moda', $estadisticas['moda']]);
            fputcsv($file, ['Media', $estadisticas['media']]);
            fputcsv($file, ['Rango', $estadisticas['rango']]);
            fputcsv($file, []);
            
            // Datos por opción
            fputcsv($file, ['Opción', 'Frecuencia', 'Porcentaje']);
            foreach ($estadisticas['datos'] as $dato) {
                fputcsv($file, [
                    $dato['opcion'],
                    $dato['frecuencia'],
                    $dato['porcentaje'] . '%'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Eliminar encuesta
     */
    public function destroy(Encuesta $encuesta)
    {
        $encuesta->delete();
        
        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta eliminada exitosamente');
    }

    /**
     * Generar datos de ejemplo
     */
    public function generarDatosEjemplo()
    {
        DB::transaction(function () {
            // Encuesta 1: Fin de semana
            $encuesta1 = Encuesta::create([
                'titulo' => '¿Cómo prefieren pasar el fin de semana?',
                'descripcion' => 'Encuesta sobre actividades preferidas durante el fin de semana',
                'activa' => true,
            ]);

            $opciones1 = [
                ['texto' => 'Ver películas', 'color' => '#EF4444'],
                ['texto' => 'Salir con amigos', 'color' => '#10B981'],
                ['texto' => 'Practicar deporte', 'color' => '#3B82F6'],
                ['texto' => 'Descansar', 'color' => '#F59E0B'],
                ['texto' => 'Usar redes sociales', 'color' => '#8B5CF6'],
                ['texto' => 'Actividades religiosas', 'color' => '#EC4899'],
            ];

            foreach ($opciones1 as $op) {
                $opcion = Opcion::create(array_merge($op, ['encuesta_id' => $encuesta1->id]));
                
                // Generar respuestas simuladas
                $cantidadRespuestas = rand(5, 25);
                for ($i = 0; $i < $cantidadRespuestas; $i++) {
                    Respuesta::create([
                        'opcion_id' => $opcion->id,
                        'participante' => 'Estudiante ' . rand(1, 100),
                        'fecha_respuesta' => now()->subDays(rand(0, 7)),
                    ]);
                }
            }

            // Encuesta 2: Tecnologías web
            $encuesta2 = Encuesta::create([
                'titulo' => 'Tecnologías favoritas para desarrollo web',
                'descripcion' => 'Frameworks y librerías preferidos para proyectos web',
                'activa' => true,
            ]);

            $opciones2 = [
                ['texto' => 'React', 'color' => '#61DAFB'],
                ['texto' => 'Vue.js', 'color' => '#42B883'],
                ['texto' => 'Angular', 'color' => '#DD0031'],
                ['texto' => 'Laravel', 'color' => '#FF2D20'],
                ['texto' => 'Django', 'color' => '#092E20'],
            ];

            foreach ($opciones2 as $op) {
                $opcion = Opcion::create(array_merge($op, ['encuesta_id' => $encuesta2->id]));
                
                $cantidadRespuestas = rand(10, 30);
                for ($i = 0; $i < $cantidadRespuestas; $i++) {
                    Respuesta::create([
                        'opcion_id' => $opcion->id,
                        'participante' => 'Desarrollador ' . rand(1, 100),
                        'fecha_respuesta' => now()->subDays(rand(0, 14)),
                    ]);
                }
            }
        });

        return redirect()->route('encuestas.index')
            ->with('success', 'Datos de ejemplo generados exitosamente');
    }

    /**
     * Generar color aleatorio en formato hexadecimal
     */
    private function generarColorAleatorio(): string
    {
        $colores = [
            '#EF4444', '#10B981', '#3B82F6', '#F59E0B', 
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
        ];
        
        return $colores[array_rand($colores)];
    }
}