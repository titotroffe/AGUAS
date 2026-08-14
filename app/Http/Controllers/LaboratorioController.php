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
        $insumos = LaboratorioInsumo::orderBy('created_at', 'desc')->take(24)->get();
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

        return view('laboratorio.index', compact('insumos', 'aguaCruda', 'productoTerminado', 'pozos', 'ultimasNovedades', 'novedadesRecientes'));
    }

    public function storeInsumo(Request $request)
    {
        $data = $request->all();
        $data['preparacion_archivo_contramuestra'] = $request->has('preparacion_archivo_contramuestra');
        
        LaboratorioInsumo::create($data);
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
    
    public function destroyInsumo($id)
    {
        LaboratorioInsumo::findOrFail($id)->delete();
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
