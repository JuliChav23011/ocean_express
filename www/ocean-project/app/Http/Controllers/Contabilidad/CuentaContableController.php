<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuentaContableController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $query = CuentaContable::with('cuentaPadre')
            ->withCount('detallesAsientos')
            ->where('empresa_id', $empresa->id);

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->where('codigo', 'ilike', "%{$termino}%")
                  ->orWhere('nombre', 'ilike', "%{$termino}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('activa', $request->estado === 'activa');
        }

        $cuentas = $query->orderBy('codigo')->get();

        return view('contabilidad.cuentas.index', compact('cuentas', 'empresa'));
    }

    public function create()
    {
        $empresa = Empresa::first();
        $cuentasPadre = CuentaContable::where('empresa_id', $empresa->id)
            ->where('activa', true)
            ->orderBy('codigo')
            ->get();

        return view('contabilidad.cuentas.create', compact('cuentasPadre', 'empresa'));
    }

    public function store(Request $request)
    {
        $empresa = Empresa::first();

        $validated = $request->validate([
            'codigo' => [
                'required', 'string', 'max:30',
                Rule::unique('cuentas_contables')->where('empresa_id', $empresa->id),
            ],
            'nombre' => 'required|string|max:200',
            'cuenta_padre_id' => 'nullable|exists:cuentas_contables,id',
            'tipo' => 'required|in:activo,pasivo,patrimonio,ingreso,costo,gasto',
            'naturaleza' => 'required|in:deudora,acreedora',
            'permite_movimientos' => 'required|boolean',
            'activa' => 'sometimes|boolean',
        ], [
            'codigo.unique' => 'Ya existe una cuenta registrada con ese código contable.',
        ]);

        CuentaContable::create([
            'empresa_id' => $empresa->id,
            'cuenta_padre_id' => $validated['cuenta_padre_id'] ?? null,
            'codigo' => trim($validated['codigo']),
            'nombre' => trim($validated['nombre']),
            'tipo' => $validated['tipo'],
            'naturaleza' => $validated['naturaleza'],
            'permite_movimientos' => (bool) $validated['permite_movimientos'],
            'activa' => $request->boolean('activa', true),
        ]);

        return redirect()->route('contabilidad.cuentas.index')
            ->with('success', 'Cuenta contable creada correctamente.');
    }

    public function edit(CuentaContable $cuenta)
    {
        $empresa = Empresa::first();
        $cuentasPadre = CuentaContable::where('empresa_id', $empresa->id)
            ->where('id', '!=', $cuenta->id)
            ->orderBy('codigo')
            ->get();

        return view('contabilidad.cuentas.edit', compact('cuenta', 'cuentasPadre'));
    }

    public function update(Request $request, CuentaContable $cuenta)
    {
        $validated = $request->validate([
            'codigo' => [
                'required', 'string', 'max:30',
                Rule::unique('cuentas_contables')
                    ->where('empresa_id', $cuenta->empresa_id)
                    ->ignore($cuenta->id),
            ],
            'nombre' => 'required|string|max:200',
            'cuenta_padre_id' => 'nullable|exists:cuentas_contables,id',
            'tipo' => 'required|in:activo,pasivo,patrimonio,ingreso,costo,gasto',
            'naturaleza' => 'required|in:deudora,acreedora',
            'permite_movimientos' => 'required|boolean',
            'activa' => 'sometimes|boolean',
        ], [
            'codigo.unique' => 'Otro registro ya utiliza ese código contable.',
        ]);

        if (($validated['cuenta_padre_id'] ?? null) == $cuenta->id) {
            return back()->withInput()->with('error', 'Una cuenta no puede ser su propia cuenta padre.');
        }

        if ($cuenta->tieneMovimientos() && !$validated['permite_movimientos']) {
            return back()->withInput()->with('error', 'No puede cambiar a cuenta agrupadora porque ya tiene movimientos contables registrados.');
        }

        $cuenta->update([
            'cuenta_padre_id' => $validated['cuenta_padre_id'] ?? null,
            'codigo' => trim($validated['codigo']),
            'nombre' => trim($validated['nombre']),
            'tipo' => $validated['tipo'],
            'naturaleza' => $validated['naturaleza'],
            'permite_movimientos' => (bool) $validated['permite_movimientos'],
            'activa' => $request->boolean('activa'),
        ]);

        return redirect()->route('contabilidad.cuentas.index')
            ->with('success', 'Cuenta contable actualizada correctamente.');
    }

    public function toggleEstado(CuentaContable $cuenta)
    {
        $cuenta->update(['activa' => !$cuenta->activa]);
        $estado = $cuenta->activa ? 'activada' : 'desactivada';

        return back()->with('success', "La cuenta {$cuenta->codigo} - {$cuenta->nombre} fue {$estado}.");
    }

    public function destroy(CuentaContable $cuenta)
    {
        if ($cuenta->tieneMovimientos()) {
            return back()->with('error', 'No se puede eliminar la cuenta porque tiene asientos contables asociados. Puede desactivarla en su lugar.');
        }

        if ($cuenta->subcuentas()->exists()) {
            return back()->with('error', 'No se puede eliminar la cuenta porque tiene subcuentas dependientes.');
        }

        $cuenta->delete();

        return redirect()->route('contabilidad.cuentas.index')
            ->with('success', 'Cuenta eliminada correctamente.');
    }
}