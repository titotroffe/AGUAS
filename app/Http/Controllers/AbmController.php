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

        // QA-02: Validar que el nombre de tabla sea estrictamente alfanumérico
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            abort(422, 'Nombre de tabla inválido.');
        }

        $columns = Schema::getColumnListing($table);
        
        // Obtener tipos de datos y nulabilidad
        $columnTypes = [];
        $requiredColumns = [];
        $describe = DB::select("DESCRIBE `$table`");
        foreach ($describe as $col) {
            $columnTypes[$col->Field] = $col->Type;
            $type = strtolower($col->Type);
            $isBoolean = str_contains($type, 'tinyint(1)') || str_contains($type, 'bool');
            if (!$isBoolean && $col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment' && !in_array($col->Field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                $requiredColumns[] = $col->Field;
            }
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

        return view('jefatura.abm.table', compact('table', 'columns', 'columnTypes', 'records', 'foreignData', 'requiredColumns'));
    }

    public function store(Request $request, $table)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        // QA-02: Validar que el nombre de tabla sea estrictamente alfanumérico
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            abort(422, 'Nombre de tabla inválido.');
        }

        // Validaciones dinámicas basadas en las columnas que son NOT NULL
        $describe = DB::select("DESCRIBE `$table`");
        $columnTypes = [];
        $rules = [];
        $messages = [];
        foreach ($describe as $col) {
            $field = $col->Field;
            $type = strtolower($col->Type);
            $columnTypes[$field] = $type;

            if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $isBoolean = str_contains($type, 'tinyint(1)') || str_contains($type, 'bool');
            if ($isBoolean) {
                $rules[$field] = 'nullable|boolean';
                continue;
            }

            // QA-04: Derivar el largo máximo del tipo de columna de la BD
            $isString = str_contains($type, 'varchar') || str_contains($type, 'char')
                     || str_contains($type, 'text') || str_contains($type, 'enum');
            $maxLength = null;
            if ($isString) {
                preg_match('/\((\d+)\)/', $type, $lengthMatch);
                $maxLength = isset($lengthMatch[1]) ? (int)$lengthMatch[1] : 65000;
            }

            if ($col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment') {
                $rules[$field] = $maxLength ? "required|max:{$maxLength}" : 'required';
                $messages["{$field}.required"] = "El campo '" . ucfirst($field) . "' es obligatorio.";
                if ($maxLength) {
                    $messages["{$field}.max"] = "El campo '" . ucfirst($field) . "' no puede superar los {$maxLength} caracteres.";
                }
            } elseif ($maxLength) {
                $rules[$field] = "nullable|max:{$maxLength}";
                $messages["{$field}.max"] = "El campo '" . ucfirst($field) . "' no puede superar los {$maxLength} caracteres.";
            }
        }
        $request->validate($rules, $messages);

        $columns = Schema::getColumnListing($table);
        $data = $request->except(['_token', '_method']);
        
        $insertData = [];
        foreach ($columns as $column) {
            if ($column === 'id' || $column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                continue;
            }

            $isBooleanCol = str_contains($columnTypes[$column] ?? '', 'tinyint(1)') 
                         || str_contains($columnTypes[$column] ?? '', 'bool');

            if (array_key_exists($column, $data)) {
                $val = $data[$column];
                $insertData[$column] = $isBooleanCol ? (($val == '1' || $val === true || $val === 1) ? 1 : 0) : $val;
            } else {
                if ($isBooleanCol) {
                    $insertData[$column] = 0;
                }
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
            Log::error("ABM store error [{$table}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al guardar el registro. Contacte al administrador.')->withInput();
        }
    }

    public function update(Request $request, $table, $id)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        // Validaciones dinámicas basadas en las columnas que son NOT NULL
        $describe = DB::select("DESCRIBE `$table`");
        $columnTypes = [];
        $rules = [];
        $messages = [];
        foreach ($describe as $col) {
            $field = $col->Field;
            $type = strtolower($col->Type);
            $columnTypes[$field] = $type;

            if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $isBoolean = str_contains($type, 'tinyint(1)') || str_contains($type, 'bool');
            if ($isBoolean) {
                $rules[$field] = 'nullable|boolean';
                continue;
            }

            // QA-04: Derivar el largo máximo del tipo de columna de la BD
            $isString = str_contains($type, 'varchar') || str_contains($type, 'char')
                     || str_contains($type, 'text') || str_contains($type, 'enum');
            $maxLength = null;
            if ($isString) {
                preg_match('/\((\d+)\)/', $type, $lengthMatch);
                $maxLength = isset($lengthMatch[1]) ? (int)$lengthMatch[1] : 65000;
            }

            if ($col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment') {
                $rules[$field] = $maxLength ? "required|max:{$maxLength}" : 'required';
                $messages["{$field}.required"] = "El campo '" . ucfirst($field) . "' es obligatorio.";
                if ($maxLength) {
                    $messages["{$field}.max"] = "El campo '" . ucfirst($field) . "' no puede superar los {$maxLength} caracteres.";
                }
            } elseif ($maxLength) {
                $rules[$field] = "nullable|max:{$maxLength}";
                $messages["{$field}.max"] = "El campo '" . ucfirst($field) . "' no puede superar los {$maxLength} caracteres.";
            }
        }
        $request->validate($rules, $messages);

        $columns = Schema::getColumnListing($table);
        $data = $request->except(['_token', '_method']);
        
        $updateData = [];
        foreach ($columns as $column) {
            if ($column === 'id' || $column === 'created_at' || $column === 'updated_at' || $column === 'deleted_at') {
                continue;
            }

            $isBooleanCol = str_contains($columnTypes[$column] ?? '', 'tinyint(1)') 
                         || str_contains($columnTypes[$column] ?? '', 'bool');

            if (array_key_exists($column, $data)) {
                $val = $data[$column];
                $updateData[$column] = $isBooleanCol ? (($val == '1' || $val === true || $val === 1) ? 1 : 0) : $val;
            } else {
                $updateData[$column] = $isBooleanCol ? 0 : null;
            }
        }

        if (in_array('updated_at', $columns)) {
            $updateData['updated_at'] = now();
        }

        try {
            DB::table($table)->where('id', $id)->update($updateData);
            return redirect()->route('jefatura.abm.show', $table)->with('success', 'Registro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error("ABM update error [{$table}#{$id}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el registro. Contacte al administrador.')->withInput();
        }
    }

    public function destroy($table, $id)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        try {
            $deleted = DB::table($table)->where('id', $id)->delete();
            if (!$deleted) {
                return redirect()->route('jefatura.abm.show', $table)->with('error', 'El registro no fue encontrado o ya fue eliminado.');
            }
            return redirect()->route('jefatura.abm.show', $table)->with('success', 'Registro eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? 0;
            if ($errorCode == 1451 || $e->getCode() == 23000) {
                // Intentar extraer el nombre de la tabla dependiente del mensaje de error
                preg_match('/CONSTRAINT `[^`]+` FOREIGN KEY \([^)]+\) REFERENCES `([^`]+)`/', $e->getMessage(), $matchesRef);
                preg_match('/a foreign key constraint fails \(`[^`]+`\.`([^`]+)`/', $e->getMessage(), $matchesTable);
                
                $tableName = $matchesTable[1] ?? 'otra tabla';
                
                return redirect()->back()->with('error', "No se puede eliminar este registro. Hay datos asociados que dependen de él en la tabla: '{$tableName}'. Por favor, elimine esos datos primero.");
            }
            Log::error("ABM destroy FK error [{$table}#{$id}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error de base de datos al eliminar. Contacte al administrador.');
        } catch (\Exception $e) {
            Log::error("ABM destroy error [{$table}#{$id}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al eliminar. Contacte al administrador.');
        }
    }

    public function addColumn(Request $request, $table)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table)) {
            abort(403);
        }

        $request->validate([
            'column_name' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|max:64',
            'column_type' => 'required|in:string,integer,float,date,boolean',
            'is_required' => 'nullable|boolean'
        ]);

        $name = strtolower($request->column_name);
        $type = $request->column_type;

        if (Schema::hasColumn($table, $name)) {
            return redirect()->back()->with('error', 'La columna ya existe en esta tabla.');
        }

        $isRequired = $request->has('is_required');

        try {
            Schema::table($table, function (\Illuminate\Database\Schema\Blueprint $t) use ($name, $type, $isRequired) {
                $col = null;
                switch ($type) {
                    case 'integer':
                        $col = $t->integer($name);
                        break;
                    case 'float':
                        $col = $t->float($name);
                        break;
                    case 'date':
                        $col = $t->date($name);
                        break;
                    case 'boolean':
                        $col = $t->boolean($name)->default(0);
                        break;
                    case 'string':
                    default:
                        $col = $t->string($name);
                        break;
                }
                
                if (!$isRequired && $type !== 'boolean') {
                    $col->nullable();
                }
            });
            return redirect()->route('jefatura.abm.show', $table)->with('success', "Columna '{$name}' añadida exitosamente.");
        } catch (\Exception $e) {
            Log::error("ABM addColumn error [{$table}.{$name}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al añadir la columna. Contacte al administrador.');
        }
    }

    public function updateColumn(Request $request, $table, $column)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            abort(403);
        }

        $request->validate([
            'new_column_name' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|max:64',
        ]);

        $newName = strtolower($request->new_column_name);

        if ($newName !== $column && Schema::hasColumn($table, $newName)) {
            return redirect()->back()->with('error', 'Ya existe una columna con ese nombre.');
        }

        try {
            if ($newName !== $column) {
                Schema::table($table, function (\Illuminate\Database\Schema\Blueprint $t) use ($column, $newName) {
                    $t->renameColumn($column, $newName);
                });
            }
            return redirect()->route('jefatura.abm.show', $table)->with('success', "Columna actualizada exitosamente.");
        } catch (\Exception $e) {
            Log::error("ABM updateColumn error [{$table}.{$column}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar la columna. Contacte al administrador.');
        }
    }

    public function destroyColumn($table, $column)
    {
        if (in_array($table, $this->blacklistedTables) || !Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            abort(403);
        }

        // QA-02: Validar que el nombre de columna sea estrictamente alfanumérico
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            abort(422, 'Nombre de columna inválido.');
        }

        $protectedColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
        if (in_array(strtolower($column), $protectedColumns)) {
            return redirect()->back()->with('error', "No se puede eliminar la columna protegida '{$column}'.");
        }

        // Verificar si hay datos asociados (que no sean nulos ni strings vacíos)
        // QA-02: $column ya fue validado con regex, seguro para usar en backticks
        $hasData = DB::table($table)
            ->whereNotNull($column)
            ->whereRaw('CAST(`' . $column . '` AS CHAR) != \'\'') // nombre saneado por regex
            ->exists();
            
        if ($hasData) {
            return redirect()->back()->with('error', "No se puede eliminar la columna '{$column}' porque ya contiene registros asociados.");
        }

        try {
            Schema::table($table, function (\Illuminate\Database\Schema\Blueprint $t) use ($column) {
                $t->dropColumn($column);
            });
            return redirect()->route('jefatura.abm.show', $table)->with('success', "Columna '{$column}' eliminada exitosamente.");
        } catch (\Exception $e) {
            Log::error("ABM destroyColumn error [{$table}.{$column}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar la columna. Contacte al administrador.');
        }
    }
}
