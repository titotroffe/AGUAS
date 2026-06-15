<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\RegistroPresion;
use App\Models\RegistroFiltro;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
    // Mostrar la pantalla principal
    public function index()
    {
        return view('operadores.index');
    }

    // Guardar los datos del formulario central
    public function store(Request $request)
    {
        // 1. Buscamos si el operador ya tiene un turno abierto hoy, sino le creamos uno.
        $turno = Turno::firstOrCreate(
            ['user_id' => Auth::id(), 'estado' => 'abierto'],
            ['hora_inicio' => $request->hora_inicio ?? now()->format('H:i')]
        );

        // 2. Guardamos las presiones asociadas a ese turno
        RegistroPresion::create([
            'turno_id' => $turno->id,
            'presion_tanque' => $request->presion_tanque,
            'presion_planta' => $request->presion_planta,
            'presion_falcon' => $request->presion_falcon,
            'nivel_cisterna' => $request->nivel_cisterna,
        ]);

        // 3. Guardamos el estado de los filtros (los checkboxes envían 'on' si están marcados)
        RegistroFiltro::create([
            'turno_id' => $turno->id,
            'norte_1' => $request->has('norte_1'),
            'norte_2' => $request->has('norte_2'),
            'norte_3' => $request->has('norte_3'),
            'sur_1' => $request->has('sur_1'),
            'sur_2' => $request->has('sur_2'),
            'sur_3' => $request->has('sur_3'),
        ]);

        // Recargamos la página con un mensaje de éxito invisible por ahora
        return back()->with('success', 'Datos registrados correctamente');
    }
}