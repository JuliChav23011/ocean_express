<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;
use Illuminate\Http\Request;

class PeriodoContableController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::withCount('asientosContables')
            ->where('empresa_id', $empresa->id)
            ->orderByDesc('fecha_inicio')
            ->paginate(15)
            ->withQueryString();

        return view('contabilidad.periodos.index', compact('periodos', 'empresa'));
    }

    public function store(Request $request)
    {
        $empresa = Empresa::first();

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
        ]);

        $traslape = PeriodoContable::where('empresa_id', $empresa->id)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('fecha_inicio', [$validated['fecha_inicio'], $validated['fecha_fin']])
                  ->orWhereBetween('fecha_fin', [$validated['fecha_inicio'], $validated['fecha_fin']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('fecha_inicio', '<=', $validated['fecha_inicio'])
                         ->where('fecha_fin', '>=', $validated['fecha_fin']);
                  });
            })->exists();

        if ($traslape) {
            return back()->withInput()->with('error', 'Las fechas ingresadas se traslapan con un período contable ya registrado.');
        }

        PeriodoContable::create([
            'empresa_id' => $empresa->id,
            'nombre' => trim($validated['nombre']),
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'estado' => 'abierto',
        ]);

        return redirect()->route('contabilidad.periodos.index')
            ->with('success', 'Período contable registrado correctamente.');
    }

    public function update(Request $request, PeriodoContable $periodo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        if ($periodo->asientosContables()->exists()) {
            $fueraRango = $periodo->asientosContables()
                ->where(function ($q) use ($validated) {
                    $q->where('fecha', '<', $validated['fecha_inicio'])
                      ->orWhere('fecha', '>', $validated['fecha_fin']);
                })->exists();

            if ($fueraRango) {
                return back()->with('error', 'Existen asientos registrados fuera del nuevo rango de fechas.');
            }
        }

        $periodo->update($validated);

        return redirect()->route('contabilidad.periodos.index')
            ->with('success', 'Período contable actualizado.');
    }

    public function cambiarEstado(PeriodoContable $periodo)
    {
        $nuevoEstado = $periodo->estado === 'abierto' ? 'cerrado' : 'abierto';
        $periodo->update(['estado' => $nuevoEstado]);

        return back()->with('success', "El período {$periodo->nombre} ahora está {$nuevoEstado}.");
    }
}