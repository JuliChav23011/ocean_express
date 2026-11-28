<?php

namespace App\Http\Controllers\Economia;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Economia\EstudioInversion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstudioInversionController extends Controller
{
    public function index()
    {
        $estudios = EstudioInversion::with('empresa')
            ->latest()
            ->paginate(10);

        return view('economia.estudios.index', compact('estudios'));
    }

    public function create()
    {
        $empresas = Empresa::all();

        return view('economia.estudios.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        EstudioInversion::create($datos);

        return redirect()
            ->route('economia.estudios.index')
            ->with('success', 'Estudio registrado correctamente.');
    }

    public function show(EstudioInversion $estudio)
    {
        $estudio->load('empresa', 'alternativas');

        return view('economia.estudios.show', compact('estudio'));
    }

    public function edit(EstudioInversion $estudio)
    {
        $empresas = Empresa::all();

        return view(
            'economia.estudios.edit',
            compact('estudio', 'empresas')
        );
    }

    public function update(
        Request $request,
        EstudioInversion $estudio
    ) {
        $datos = $this->validar($request, $estudio);

        $estudio->update($datos);

        return redirect()
            ->route('economia.estudios.index')
            ->with('success', 'Estudio actualizado correctamente.');
    }

    public function destroy(EstudioInversion $estudio)
    {
        if ($estudio->alternativas()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar un estudio con alternativas registradas.'
            );
        }

        $estudio->delete();

        return redirect()
            ->route('economia.estudios.index')
            ->with('success', 'Estudio eliminado correctamente.');
    }

    private function validar(
        Request $request,
        ?EstudioInversion $estudio = null
    ): array {
        $codigoUnico = Rule::unique(
            'estudios_inversion',
            'codigo'
        )->where('empresa_id', $request->input('empresa_id'));

        if ($estudio) {
            $codigoUnico->ignore($estudio->id);
        }

        return $request->validate([
            'empresa_id' => [
                'required',
                'exists:empresas,id'
            ],
            'codigo' => [
                'required',
                'string',
                'max:30',
                $codigoUnico
            ],
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'objetivo' => 'nullable|string',
            'fecha_estudio' => 'required|date',
            'horizonte_anios' => 'required|integer|min:1',
            'tasa_descuento' => 'required|numeric|min:0|max:9999.9999',
            'tasa_impuesto' => 'required|numeric|min:0|max:100',
            'tasa_inflacion' => 'required|numeric|min:0|max:9999.9999',
            'moneda' => 'required|string|size:3',
            'estado' => 'required|in:borrador,en_evaluacion,finalizado',
            'supuestos' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);
    }
}
