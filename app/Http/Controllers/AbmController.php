<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AbmController extends Controller
{
    // Lista de tablas que NO queremos que se puedan editar desde el ABM
    protected $blacklistedTables = [
        'migrations', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks',
        'failed_jobs', 'jobs', 'job_batches', 'personal_access_tokens'
    ];

    public function index()
    {
        // Obtener todas las tablas de la base de datos
        $tables = DB::select('SHOW TABLES');
        
        $availableTables = [];
        foreach ($tables as $tableInfo) {
            // Obtenemos el valor de la primera (y única) propiedad devuelta por MySQL
            $tableName = current((array)$tableInfo);
            if (!in_array($tableName, $this->blacklistedTables)) {
                $availableTables[] = $tableName;
            }
        }

        return view('jefatura.abm.index', compact('availableTables'));
    }

    public function showTable($table)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(404, 'Tabla no encontrada o acceso denegado.');
        }

        $columns = Schema::getColumnListing($table);
        
        // Obtener tipos de datos
        $columnTypes = [];
        $describe = DB::select("DESCRIBE `$table`");
        foreach ($describe as $col) {
            $columnTypes[$col->Field] = $col->Type;
        }

        // Obtener foreign keys y sus datos relacionados
        $foreignKeys = Schema::getForeignKeys($table);
        $foreignData = [];
        
        foreach ($foreignKeys as $fk) {
            $localColumn = $fk['columns'][0] ?? null;
            $foreignTable = $fk['foreign_table'];
            $foreignCol = $fk['foreign_columns'][0] ?? 'id';
            
            if ($localColumn && $foreignTable && Schema::hasTable($foreignTable)) {
                // Try to find a display column
                $foreignTableCols = Schema::getColumnListing($foreignTable);
                $displayCol = $foreignCol; // fallback to ID
                $candidates = ['nombre', 'name', 'titulo', 'title', 'descripcion', 'description'];
                
                foreach ($candidates as $cand) {
                    if (in_array($cand, $foreignTableCols)) {
                        $displayCol = $cand;
                        break;
                    }
                }
                
                // Fetch data
                $data = DB::table($foreignTable)->select($foreignCol, $displayCol)->get();
                $foreignData[$localColumn] = [
                    'table' => $foreignTable,
                    'key' => $foreignCol,
                    'display' => $displayCol,
                    'options' => $data
                ];
            }
        }

        $records = DB::table($table)->paginate(15);

        return view('jefatura.abm.table', compact('table', 'columns', 'columnTypes', 'records', 'foreignData'));
    }

    public function store(Request $request, $table)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        $columns = Schema::getColumnListing($table);
        $data = $request->except(['_token', '_method']);
        
        $insertData = [];
        foreach ($columns as $column) {
            if ($column === 'id' || $column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                continue;
            }
            if (array_key_exists($column, $data)) {
                $insertData[$column] = $data[$column];
            } else {
                // If checkbox unchecked, it might not be in request. We should handle booleans.
                // For simplicity in a dynamic ABM, if it's missing we can set it to null or 0 if it's a boolean.
                // We will let the view pass a hidden field or handle it explicitly if needed.
            }
        }

        if (in_array('created_at', $columns)) {
            $insertData['created_at'] = now();
        }
        if (in_array('updated_at', $columns)) {
            $insertData['updated_at'] = now();
        }

        try {
            DB::table($table)->insert($insertData);
            return redirect()->route('jefatura.abm.show', $table)->with('success', 'Registro creado correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el registro: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $table, $id)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        $columns = Schema::getColumnListing($table);
        $data = $request->except(['_token', '_method']);
        
        $updateData = [];
        foreach ($columns as $column) {
            if ($column === 'id' || $column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                continue;
            }
            // For updates, we need to carefully handle missing values which could mean a checkbox was unchecked
            // But since this is dynamic, we'll only update what's passed, except if we know it's a boolean from DB schema.
            // To simplify, if it's in the request, we update it.
            if (array_key_exists($column, $data)) {
                $updateData[$column] = $data[$column];
            } else {
                $updateData[$column] = null; // Basic approach for missing checkbox / empty fields
            }
        }

        if (in_array('updated_at', $columns)) {
            $updateData['updated_at'] = now();
        }

        try {
            DB::table($table)->where('id', $id)->update($updateData);
            return redirect()->route('jefatura.abm.show', $table)->with('success', 'Registro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($table, $id)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        try {
            DB::table($table)->where('id', $id)->delete();
            return redirect()->route('jefatura.abm.show', $table)->with('success', 'Registro eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar. Es posible que el registro esté siendo usado en otra tabla.');
        }
    }

    public function addColumn(Request $request, $table)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        $request->validate([
            'column_name' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|max:64',
            'column_type' => 'required|in:string,integer,float,date,boolean'
        ]);

        $name = strtolower($request->column_name);
        $type = $request->column_type;

        if (Schema::hasColumn($table, $name)) {
            return redirect()->back()->with('error', 'La columna ya existe en esta tabla.');
        }

        try {
            Schema::table($table, function (\Illuminate\Database\Schema\Blueprint $t) use ($name, $type) {
                switch ($type) {
                    case 'integer':
                        $t->integer($name)->nullable();
                        break;
                    case 'float':
                        $t->float($name)->nullable();
                        break;
                    case 'date':
                        $t->date($name)->nullable();
                        break;
                    case 'boolean':
                        $t->boolean($name)->nullable()->default(0);
                        break;
                    case 'string':
                    default:
                        $t->string($name)->nullable();
                        break;
                }
            });
            return redirect()->route('jefatura.abm.show', $table)->with('success', "Columna '{$name}' añadida exitosamente.");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al añadir columna: ' . $e->getMessage());
        }
    }
}
