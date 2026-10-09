<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $fecha = $request->query('fecha', now()->toDateString());
        $vista = $request->query('vista', 'dia');

        $consulta = Cita::with(['paciente', 'servicio'])
            ->where('estado', '!=', 'cancelada');

        if ($vista === 'semana') {
            $inicio = Carbon::parse($fecha)->startOfWeek()->toDateString();
            $fin = Carbon::parse($fecha)->endOfWeek()->toDateString();
            $consulta->whereBetween('fecha', [$inicio, $fin]);
        } else {
            $consulta->whereDate('fecha', $fecha);
        }

        $citas = $consulta->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('dentista.agenda', compact('citas', 'fecha', 'vista'));
    }

    public function create(): View
    {
        $servicios = Servicio::orderBy('nombre')->get();

        return view('dentista.citas.create', compact('servicios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos) {
            $paciente = Paciente::updateOrCreate(
                ['telefono' => $datos['telefono']],
                ['nombre' => $datos['nombre']]
            );

            $servicio = Servicio::findOrFail($datos['servicio_id']);
            $inicio = Carbon::parse($datos['fecha'].' '.$datos['hora_inicio']);
            $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

            $this->comprobarEmpalme(
                $datos['fecha'],
                $inicio->format('H:i:s'),
                $fin->format('H:i:s')
            );

            Cita::create([
                'paciente_id' => $paciente->id,
                'servicio_id' => $servicio->id,
                'fecha' => $datos['fecha'],
                'hora_inicio' => $inicio->format('H:i:s'),
                'hora_fin' => $fin->format('H:i:s'),
                'estado' => 'pendiente',
                'notas' => $datos['notas'] ?? null,
            ]);
        });

        return redirect()->route('dentista.agenda')
            ->with('success', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita): View
    {
        $cita->load('paciente');
        $servicios = Servicio::orderBy('nombre')->get();

        return view('dentista.citas.edit', compact('cita', 'servicios'));
    }

    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos, $cita) {
            $paciente = Paciente::updateOrCreate(
                ['telefono' => $datos['telefono']],
                ['nombre' => $datos['nombre']]
            );

            $servicio = Servicio::findOrFail($datos['servicio_id']);
            $inicio = Carbon::parse($datos['fecha'].' '.$datos['hora_inicio']);
            $fin = $inicio->copy()->addMinutes($servicio->duracion_minutos);

            $this->comprobarEmpalme(
                $datos['fecha'],
                $inicio->format('H:i:s'),
                $fin->format('H:i:s'),
                $cita->id
            );

            $cita->update([
                'paciente_id' => $paciente->id,
                'servicio_id' => $servicio->id,
                'fecha' => $datos['fecha'],
                'hora_inicio' => $inicio->format('H:i:s'),
                'hora_fin' => $fin->format('H:i:s'),
                'notas' => $datos['notas'] ?? null,
            ]);
        });

        return redirect()->route('dentista.agenda')
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function cancel(Cita $cita): RedirectResponse
    {
        $cita->update(['estado' => 'cancelada']);

        return back()->with('success', 'Cita cancelada.');
    }

    public function complete(Cita $cita): RedirectResponse
    {
        if ($cita->estado === 'pendiente') {
            $cita->update(['estado' => 'completada']);
        }

        return back()->with('success', 'Cita completada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'servicio_id' => ['required', 'integer', 'exists:servicios,id'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function comprobarEmpalme(
        string $fecha,
        string $inicio,
        string $fin,
        ?int $excluir = null
    ): void {
        $consulta = Cita::whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where('hora_inicio', '<', $fin)
            ->where('hora_fin', '>', $inicio);

        if ($excluir !== null) {
            $consulta->where('id', '!=', $excluir);
        }

        if ($consulta->exists()) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El horario se empalma con otra cita.',
            ]);
        }
    }
}
