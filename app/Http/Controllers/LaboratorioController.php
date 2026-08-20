<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaboratorioInsumo;
use App\Models\LaboratorioAguaCruda;
use App\Models\LaboratorioProductoTerminado;
use App\Models\LaboratorioPozo;
use App\Models\Novedad;
use Illuminate\Support\Facades\Auth;

class LaboratorioController extends Controller
{
    public function index()
    {
        $sulfatos = \App\Models\InsumoSulfato::take(24)->get()->map(function($i) { $i->tipo_insumo = 'sulfato'; $i->nombre_insumo = 'Sulfato de Aluminio'; return $i; });
        $hipocloritos = \App\Models\InsumoHipoclorito::take(24)->get()->map(function($i) { $i->tipo_insumo = 'hipoclorito'; $i->nombre_insumo = 'Hipoclorito de Sodio'; return $i; });
        $poliaminas = \App\Models\InsumoPoliamina::take(24)->get()->map(function($i) { $i->tipo_insumo = 'poliamina'; $i->nombre_insumo = 'Poliamina'; return $i; });
        $cales = \App\Models\InsumoCal::take(24)->get()->map(function($i) { $i->tipo_insumo = 'cal_hidraulica'; $i->nombre_insumo = 'Cal Hidráulica'; return $i; });

        $insumos = $sulfatos->concat($hipocloritos)->concat($poliaminas)->concat($cales)
            ->sortByDesc('created_at')->take(24)->values();

        $aguaCruda = LaboratorioAguaCruda::orderBy('created_at', 'desc')->take(24)->get();
        $productoTerminado = LaboratorioProductoTerminado::orderBy('created_at', 'desc')->take(24)->get();
        $pozos = LaboratorioPozo::orderBy('created_at', 'desc')->take(24)->get();

        $ultimasNovedades = Novedad::with('user')->latest()->take(10)->get();

        $unreadQuery = Novedad::where('user_id', '!=', Auth::id())
            ->where('created_at', '>=', now()->subHours(16));
            
        if (Auth::user()->novedades_leidas_hasta) {
            $unreadQuery->where('created_at', '>', Auth::user()->novedades_leidas_hasta);
        }

        $novedadesRecientes = $unreadQuery->count();

        $insumoFields = [
            ['name' => 'residuo_insoluble', 'label' => 'RESIDUO INSOLUBLE', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'oxido_ferroso', 'label' => 'ÓXIDO FERROSO', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'oxido_ferrico', 'label' => 'ÓXIDO FÉRRICO', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'oxido_aluminio', 'label' => 'ÓXIDO DE ALUMINIO', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'oxidos_utiles', 'label' => 'ÓXIDOS ÚTILES', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'manganeso', 'label' => 'MANGANESO', 'classes' => 'f-sulfato', 'show' => old('tipo_insumo') == 'sulfato'],
            ['name' => 'densidad_20c', 'label' => 'DENSIDAD A 20°C', 'classes' => 'f-sulfato f-hipoclorito f-poliamina', 'show' => in_array(old('tipo_insumo'), ['sulfato', 'hipoclorito', 'poliamina'])],
            ['name' => 'cloro_activo', 'label' => 'CLORO ACTIVO', 'classes' => 'f-hipoclorito', 'show' => old('tipo_insumo') == 'hipoclorito'],
            ['name' => 'peso_litro', 'label' => 'PESO LITRO', 'classes' => 'f-cal', 'show' => old('tipo_insumo') == 'cal_hidraulica'],
        ];

        return view('laboratorio.index', compact('insumoFields', 'insumos', 'aguaCruda', 'productoTerminado', 'pozos', 'ultimasNovedades', 'novedadesRecientes'));
    }

    public function storeInsumo(Request $request)
    {
        $data = $request->all();
        $data['preparacion_archivo_contramuestra'] = $request->has('preparacion_archivo_contramuestra');
        $tipo = $request->input('tipo_insumo');

        switch ($tipo) {
            case 'sulfato':
                \App\Models\InsumoSulfato::create($data);
                break;
            case 'hipoclorito':
                \App\Models\InsumoHipoclorito::create($data);
                break;
            case 'poliamina':
                \App\Models\InsumoPoliamina::create($data);
                break;
            case 'cal_hidraulica':
                \App\Models\InsumoCal::create($data);
                break;
        }

        return redirect()->route('laboratorio.index')->with('success', 'Registro de Insumo guardado correctamente.');
    }

    public function storeAguaCruda(Request $request)
    {
        LaboratorioAguaCruda::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Agua Cruda guardado correctamente.');
    }

    public function storeProductoTerminado(Request $request)
    {
        LaboratorioProductoTerminado::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Producto Terminado guardado correctamente.');
    }

    public function storePozo(Request $request)
    {
        LaboratorioPozo::create($request->all());
        return redirect()->route('laboratorio.index')->with('success', 'Registro de Pozo guardado correctamente.');
    }
    
    public function destroyInsumo($tipo, $id)
    {
        switch ($tipo) {
            case 'sulfato':
                \App\Models\InsumoSulfato::findOrFail($id)->delete();
                break;
            case 'hipoclorito':
                \App\Models\InsumoHipoclorito::findOrFail($id)->delete();
                break;
            case 'poliamina':
                \App\Models\InsumoPoliamina::findOrFail($id)->delete();
                break;
            case 'cal_hidraulica':
                \App\Models\InsumoCal::findOrFail($id)->delete();
                break;
        }
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyAguaCruda($id)
    {
        LaboratorioAguaCruda::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyProductoTerminado($id)
    {
        LaboratorioProductoTerminado::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }
    
    public function destroyPozo($id)
    {
        LaboratorioPozo::findOrFail($id)->delete();
        return redirect()->route('laboratorio.index')->with('success', 'Registro eliminado correctamente.');
    }

    public function storeNovedad(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000',
        ], [
            'mensaje.required' => 'Debe escribir un mensaje para la novedad.',
            'mensaje.max' => 'La novedad es demasiado larga (máximo 1000 caracteres).',
        ]);

        Novedad::create([
            'user_id' => Auth::id(),
            'mensaje' => $request->mensaje,
        ]);

        return back()->with('success', 'Novedad registrada correctamente.');
    }

    public function destroyNovedad($id)
    {
        $novedad = Novedad::findOrFail($id);

        if ($novedad->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para borrar esta novedad.');
        }

        if ($novedad->created_at->lt(now()->subHours(2))) {
            return back()->with('error', 'No se puede borrar una novedad con más de 2 horas de antigüedad.');
        }

        $novedad->delete();

        return back()->with('deleted', 'Novedad eliminada correctamente.');
    }

    public function marcarLeidas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->novedades_leidas_hasta = now();
        $user->save();

        return back()->with('success', 'Novedades marcadas como leídas.');
    }
}
