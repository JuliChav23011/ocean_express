<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Administracion\Evidencia;
use App\Models\Contabilidad\AsientoContable;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AsientoContableController extends Controller
{
    public function index(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();

        $query = AsientoContable::with(['periodoContable', 'detalles', 'evidencia'])
            ->where('empresa_id', $empresa->id);

        if ($request->filled('periodo_id')) {
            $query->where('periodo_contable_id', $request->periodo_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $asientos = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('contabilidad.asientos.index', compact('asientos', 'periodos', 'empresa'));
    }

    public function ajustes(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();

        $ajustes = AsientoContable::with(['periodoContable', 'detalles.cuentaContable', 'evidencia'])
            ->where('empresa_id', $empresa->id)
            ->where('tipo', 'ajuste')
            ->when($request->filled('periodo_id'), fn($q) => $q->where('periodo_contable_id', $request->periodo_id))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('contabilidad.asientos.ajustes', compact('ajustes', 'periodos', 'empresa'));
    }

    public function create(Request $request)
    {
        $empresa = Empresa::first();
        $periodosAbiertos = PeriodoContable::where('empresa_id', $empresa->id)
            ->where('estado', 'abierto')
            ->orderByDesc('fecha_inicio')
            ->get();

        $cuentas = CuentaContable::where('empresa_id', $empresa->id)
            ->activas()
            ->deMovimiento()
            ->orderBy('codigo')
            ->get();

        $evidencias = Evidencia::where('empresa_id', $empresa->id)->orderBy('codigo')->get();

        $siguienteNum = AsientoContable::where('empresa_id', $empresa->id)->count() + 1;
        $numeroSugerido = 'P-' . str_pad((string) $siguienteNum, 3, '0', STR_PAD_LEFT);
        $tipoPreseleccionado = $request->get('tipo', 'normal');

        return view('contabilidad.asientos.create', compact(
            'empresa', 'periodosAbiertos', 'cuentas', 'evidencias', 'numeroSugerido', 'tipoPreseleccionado'
        ));
    }

    public function store(Request $request)
    {
        $empresa = Empresa::first();

        $validated = $request->validate([
            'periodo_contable_id' => 'required|exists:periodos_contables,id',
            'numero_asiento' => 'required|string|max:30',
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:1000',
            'tipo' => 'required|in:apertura,normal,ajuste,cierre',
            'accion' => 'required|in:borrador,contabilizado',
            'referencia_documento' => 'nullable|string|max:255',
            'detalles' => 'required|array|min:1',
            'detalles.*.cuenta_contable_id' => 'required|exists:cuentas_contables,id',
            'detalles.*.descripcion' => 'nullable|string|max:500',
            'detalles.*.debe' => 'nullable|numeric|min:0',
            'detalles.*.haber' => 'nullable|numeric|min:0',
        ], [
            'detalles.required' => 'Debe agregar al menos una linea de movimiento contable.',
            'detalles.min' => 'Debe agregar al menos una linea de movimiento contable.',
        ]);

        $periodo = PeriodoContable::findOrFail($validated['periodo_contable_id']);

        if (!$periodo->estaAbierto()) {
            return back()->withInput()->with('error', "El periodo {$periodo->nombre} esta CERRADO. No se permiten nuevos registros.");
        }

        if (!$periodo->contieneFecha($validated['fecha'])) {
            return back()->withInput()->with('error', "La fecha debe estar dentro del rango del periodo ({$periodo->fecha_inicio->format('d/m/Y')} al {$periodo->fecha_fin->format('d/m/Y')}).");
        }

        $duplicado = AsientoContable::where('empresa_id', $empresa->id)
            ->where('periodo_contable_id', $periodo->id)
            ->where('numero_asiento', trim($validated['numero_asiento']))
            ->exists();

        if ($duplicado) {
            return back()->withInput()->with('error', "Ya existe el asiento {$validated['numero_asiento']} en el periodo seleccionado.");
        }

        try {
            $asiento = DB::transaction(function () use ($empresa, $periodo, $validated) {
                $lineasValidas = $this->validarYPrepararDetalles($validated['detalles']);

                $asiento = AsientoContable::create([
                    'empresa_id' => $empresa->id,
                    'periodo_contable_id' => $periodo->id,
                    'numero_asiento' => trim($validated['numero_asiento']),
                    'fecha' => $validated['fecha'],
                    'concepto' => trim($validated['concepto']),
                    'tipo' => $validated['tipo'],
                    'estado' => $validated['accion'],
                    'referencia_documento' => $validated['referencia_documento'] ?: null,
                ]);

                $asiento->detalles()->createMany($lineasValidas);

                return $asiento;
            });

            $mensaje = $asiento->estado === 'contabilizado'
                ? "Asiento {$asiento->numero_asiento} contabilizado correctamente."
                : "Asiento {$asiento->numero_asiento} guardado como borrador.";

            return redirect()->route('contabilidad.asientos.show', $asiento)->with('success', $mensaje);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error al registrar el asiento: ' . $e->getMessage());
        }
    }

    public function show(AsientoContable $asiento)
    {
        $asiento->load(['periodoContable', 'detalles.cuentaContable', 'evidencia']);
        $evidencias = Evidencia::where('empresa_id', $asiento->empresa_id)->orderBy('codigo')->get();

        return view('contabilidad.asientos.show', compact('asiento', 'evidencias'));
    }

    public function edit(AsientoContable $asiento)
    {
        if ($asiento->estado !== 'borrador') {
            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('error', 'Solo se pueden editar asientos en estado BORRADOR. Los asientos contabilizados son inmutables.');
        }

        $asiento->load('detalles');
        $periodosAbiertos = PeriodoContable::where('empresa_id', $asiento->empresa_id)
            ->where('estado', 'abierto')
            ->orderByDesc('fecha_inicio')
            ->get();

        $cuentas = CuentaContable::where('empresa_id', $asiento->empresa_id)
            ->activas()
            ->deMovimiento()
            ->orderBy('codigo')
            ->get();

        $evidencias = Evidencia::where('empresa_id', $asiento->empresa_id)->orderBy('codigo')->get();

        return view('contabilidad.asientos.edit', compact('asiento', 'periodosAbiertos', 'cuentas', 'evidencias'));
    }

    public function update(Request $request, AsientoContable $asiento)
    {
        if ($asiento->estado !== 'borrador') {
            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('error', 'No se permite modificar un asiento ya contabilizado o anulado.');
        }

        $validated = $request->validate([
            'periodo_contable_id' => 'required|exists:periodos_contables,id',
            'numero_asiento' => 'required|string|max:30',
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:1000',
            'tipo' => 'required|in:apertura,normal,ajuste,cierre',
            'accion' => 'required|in:borrador,contabilizado',
            'referencia_documento' => 'nullable|string|max:255',
            'detalles' => 'required|array|min:1',
            'detalles.*.cuenta_contable_id' => 'required|exists:cuentas_contables,id',
            'detalles.*.descripcion' => 'nullable|string|max:500',
            'detalles.*.debe' => 'nullable|numeric|min:0',
            'detalles.*.haber' => 'nullable|numeric|min:0',
        ]);

        $periodo = PeriodoContable::findOrFail($validated['periodo_contable_id']);

        if (!$periodo->estaAbierto()) {
            return back()->withInput()->with('error', "El periodo {$periodo->nombre} esta cerrado.");
        }

        if (!$periodo->contieneFecha($validated['fecha'])) {
            return back()->withInput()->with('error', 'La fecha del asiento no pertenece al periodo seleccionado.');
        }

        try {
            DB::transaction(function () use ($asiento, $periodo, $validated) {
                $lineasValidas = $this->validarYPrepararDetalles($validated['detalles']);

                $asiento->update([
                    'periodo_contable_id' => $periodo->id,
                    'numero_asiento' => trim($validated['numero_asiento']),
                    'fecha' => $validated['fecha'],
                    'concepto' => trim($validated['concepto']),
                    'tipo' => $validated['tipo'],
                    'estado' => $validated['accion'],
                    'referencia_documento' => $validated['referencia_documento'] ?: null,
                ]);

                $asiento->detalles()->delete();
                $asiento->detalles()->createMany($lineasValidas);
            });

            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('success', 'Asiento actualizado correctamente.');
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }
    }

    public function contabilizar(AsientoContable $asiento)
    {
        if ($asiento->estado !== 'borrador') {
            return back()->with('error', 'Este asiento ya fue procesado.');
        }

        if (!$asiento->periodoContable->estaAbierto()) {
            return back()->with('error', 'No se puede contabilizar porque el periodo esta cerrado.');
        }

        if (!$asiento->estaCuadrado()) {
            return back()->with('error', 'El asiento esta descuadrado (Total Debe != Total Haber).');
        }

        $asiento->update(['estado' => 'contabilizado']);

        return back()->with('success', "Asiento {$asiento->numero_asiento} confirmado y contabilizado.");
    }

    public function anular(AsientoContable $asiento)
    {
        if ($asiento->estado === 'anulado') {
            return back()->with('error', 'El asiento ya se encuentra anulado.');
        }

        if (!$asiento->periodoContable->estaAbierto()) {
            return back()->with('error', 'No se puede anular un asiento perteneciente a un periodo cerrado.');
        }

        $asiento->update(['estado' => 'anulado']);

        return back()->with('success', "Asiento {$asiento->numero_asiento} anulado correctamente.");
    }

    public function asociarEvidencia(Request $request, AsientoContable $asiento)
    {
        $request->validate([
            'modo' => 'required|in:existente,nueva',
            'codigo_existente' => 'required_if:modo,existente|nullable|exists:evidencias,codigo',
            'codigo_nuevo' => 'required_if:modo,nueva|nullable|string|max:50|unique:evidencias,codigo',
            'titulo' => 'required_if:modo,nueva|nullable|string|max:255',
            'tipo_evidencia' => 'required_if:modo,nueva|nullable|in:factura,cotizacion,entrevista,fotografia,estado_financiero,registro_contable,autorizacion,otro',
            'fecha_documento' => 'nullable|date',
            'emisor' => 'nullable|string|max:255',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,docx|max:5120',
        ], [
            'codigo_existente.required_if' => 'Debes seleccionar una evidencia existente.',
            'codigo_nuevo.required_if' => 'El codigo nuevo es obligatorio cuando el modo es nueva.',
            'codigo_nuevo.unique' => 'Ya existe una evidencia con ese codigo.',
            'titulo.required_if' => 'El titulo es obligatorio cuando el modo es nueva.',
            'tipo_evidencia.required_if' => 'Debes elegir un tipo de evidencia.',
        ]);

        if ($request->modo === 'existente') {
            $asiento->update(['referencia_documento' => $request->codigo_existente]);
            return back()->with('success', "Evidencia {$request->codigo_existente} asociada al asiento.");
        }

        $rutaArchivo = null;
        $nombreArchivo = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $nombreArchivo = $file->getClientOriginalName();
            $rutaArchivo = $file->store('evidencias', 'public');
        }

        $evidencia = Evidencia::create([
            'empresa_id' => $asiento->empresa_id,
            'codigo' => trim($request->codigo_nuevo),
            'titulo' => trim($request->titulo),
            'tipo' => $request->tipo_evidencia,
            'fecha_documento' => $request->fecha_documento,
            'emisor' => $request->emisor,
            'ruta_archivo' => $rutaArchivo,
            'nombre_archivo' => $nombreArchivo,
            'verificada' => true,
        ]);

        $asiento->update(['referencia_documento' => $evidencia->codigo]);

        return back()->with('success', "Evidencia {$evidencia->codigo} registrada y vinculada al asiento.");
    }

    public function descargarEvidencia(Evidencia $evidencia)
    {
        if (!$evidencia->ruta_archivo || !Storage::disk('public')->exists($evidencia->ruta_archivo)) {
            return back()->with('error', 'Esta evidencia es un registro documental de campo sin archivo digital adjunto.');
        }

        return Storage::disk('public')->download($evidencia->ruta_archivo, $evidencia->nombre_archivo);
    }

    private function validarYPrepararDetalles(array $detallesInput): array
    {
        $lineas = [];
        $totalDebe = 0.0;
        $totalHaber = 0.0;

        foreach ($detallesInput as $index => $item) {
            $debe = round((float) ($item['debe'] ?? 0), 2);
            $haber = round((float) ($item['haber'] ?? 0), 2);

            if ($debe < 0 || $haber < 0) {
                throw ValidationException::withMessages([
                    'detalles' => "La linea #" . ($index + 1) . " contiene valores negativos.",
                ]);
            }

            if ($debe == 0 && $haber == 0) {
                throw ValidationException::withMessages([
                    'detalles' => "La linea #" . ($index + 1) . " debe tener un monto mayor a cero en Debe o Haber.",
                ]);
            }

            if ($debe > 0 && $haber > 0) {
                throw ValidationException::withMessages([
                    'detalles' => "La linea #" . ($index + 1) . " no puede tener valores simultaneos en Debe y Haber.",
                ]);
            }

            $cuenta = CuentaContable::find($item['cuenta_contable_id']);
            if (!$cuenta || !$cuenta->activa || !$cuenta->permite_movimientos) {
                throw ValidationException::withMessages([
                    'detalles' => "La cuenta seleccionada en la linea #" . ($index + 1) . " esta inactiva o es una cuenta agrupadora.",
                ]);
            }

            $totalDebe += $debe;
            $totalHaber += $haber;

            $lineas[] = [
                'cuenta_contable_id' => $cuenta->id,
                'descripcion' => $item['descripcion'] ?? null,
                'debe' => $debe,
                'haber' => $haber,
            ];
        }

        if (count($lineas) < 2) {
            throw ValidationException::withMessages([
                'detalles' => 'Todo asiento contable debe tener al menos dos movimientos (cargo y abono).',
            ]);
        }

        if (round($totalDebe, 2) !== round($totalHaber, 2)) {
            throw ValidationException::withMessages([
                'detalles' => sprintf(
                    'Asiento descuadrado: Total Debe ($%s) != Total Haber ($%s). Diferencia: $%s.',
                    number_format($totalDebe, 2),
                    number_format($totalHaber, 2),
                    number_format(abs($totalDebe - $totalHaber), 2)
                ),
            ]);
        }

        return $lineas;
    }
}