<?php

namespace App\Http\Controllers\Economia;

use App\Http\Controllers\Controller;
use App\Models\Economia\AlternativaInversion;
use App\Models\Economia\EstudioInversion;
use App\Models\Administracion\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AlternativaInversionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'estudio_id' => 'nullable|integer|exists:estudios_inversion,id'
        ]);

        $estudios = EstudioInversion::orderBy('nombre')->get();

        $alternativas = AlternativaInversion::with([
            'estudioInversion',
            'evidencia'
        ])
        ->when($request->filled('estudio_id'), function ($query) use ($request) {
            $query->where('estudio_inversion_id', $request->estudio_id);
        })
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

        return view(
            'economia.alternativas.index',
            compact('alternativas', 'estudios')
        );
    }

    public function create(Request $request)
    {
        $estudios = EstudioInversion::with('empresa')
            ->orderBy('nombre')->get();

        $evidencias = Evidencia::orderBy('codigo')->get();

        $estudioSeleccionado = $request->query('estudio_id');

        return view(
            'economia.alternativas.create',
            compact('estudios', 'evidencias', 'estudioSeleccionado')
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        AlternativaInversion::create($datos);

        return redirect()
            ->route('economia.alternativas.index')
            ->with('success', 'Alternativa registrada correctamente.');
    }

    public function show(AlternativaInversion $alternativa)
    {
        $alternativa->load([
            'estudioInversion.empresa',
            'evidencia',
            'flujosEfectivo',
            'evaluaciones'
        ]);

        return view(
            'economia.alternativas.show',
            compact('alternativa')
        );
    }

    public function edit(AlternativaInversion $alternativa)
    {
        $estudios = EstudioInversion::with('empresa')
            ->orderBy('nombre')->get();

        $evidencias = Evidencia::orderBy('codigo')->get();

        return view(
            'economia.alternativas.edit',
            compact('alternativa', 'estudios', 'evidencias')
        );
    }

    public function update(Request $request, AlternativaInversion $alternativa)
    {
        $datos = $this->validar($request, $alternativa);

        $alternativa->update($datos);

        return redirect()
            ->route('economia.alternativas.index')
            ->with('success', 'Alternativa actualizada correctamente.');
    }

    public function destroy(AlternativaInversion $alternativa)
    {
        if (
            $alternativa->flujosEfectivo()->exists() ||
            $alternativa->evaluaciones()->exists()
        ) {
            return redirect()
                ->route('economia.alternativas.index')
                ->with('error', 'No se puede eliminar una alternativa que posee flujos o evaluaciones.');
        }

        $alternativa->delete();

        return redirect()
            ->route('economia.alternativas.index')
            ->with('success', 'Alternativa eliminada correctamente.');
    }

    private function validar(
        Request $request,
        ?AlternativaInversion $alternativa = null
    ): array {
        $request->validate([
            'estudio_inversion_id' => 'required|integer|exists:estudios_inversion,id'
        ]);

        $codigoUnico = Rule::unique(
            'alternativas_inversion',
            'codigo'
        )->where(
            'estudio_inversion_id',
            $request->input('estudio_inversion_id')
        );

        if ($alternativa) {
            $codigoUnico->ignore($alternativa->id);
        }

        $datos = $request->validate([
            'estudio_inversion_id' => 'required|integer|exists:estudios_inversion,id',

            'evidencia_id' => 'nullable|integer|exists:evidencias,id',

            'codigo' => [
                'required',
                'string',
                'max:20',
                $codigoUnico
            ],

            'nombre' => 'required|string|max:200',

            'descripcion' => 'nullable|string',

            'tipo' => [
                'required',
                Rule::in([
                    'compra_activo',
                    'contratacion_servicio',
                    'otra'
                ])
            ],

            'inversion_inicial' => 'required|numeric|min:0|max:9999999999999.99',

            'costo_operativo_anual' => 'required|numeric|min:0|max:9999999999999.99',

            'ingreso_anual_estimado' => 'required|numeric|min:0|max:9999999999999.99',

            'valor_residual' => 'required|numeric|min:0|max:9999999999999.99',

            'vida_util_anios' => [
                'nullable',
                'required_unless:metodo_depreciacion,no_aplica',
                'integer',
                'min:1',
                'max:2147483647'
            ],

            'metodo_depreciacion' => [
                'required',
                Rule::in([
                    'linea_recta',
                    'suma_digitos',
                    'doble_saldo_decreciente',
                    'no_aplica'
                ])
            ],

            'clasificacion_datos' => [
                'required',
                Rule::in([
                    'real',
                    'estimado',
                    'mixto'
                ])
            ],

            'justificacion' => 'nullable|string',

            'activa' => 'required|boolean',
        ]);

        $estudio = EstudioInversion::findOrFail(
            $datos['estudio_inversion_id']
        );

        if (!empty($datos['evidencia_id'])) {
            $evidenciaValida = Evidencia::where(
                'id',
                $datos['evidencia_id']
            )
            ->where('empresa_id', $estudio->empresa_id)
            ->exists();

            if (!$evidenciaValida) {
                throw ValidationException::withMessages([
                    'evidencia_id' => 'La evidencia debe pertenecer a la misma empresa del estudio.'
                ]);
            }
        }

        return $datos;
    }
}
