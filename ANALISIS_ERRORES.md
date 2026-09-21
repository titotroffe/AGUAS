# Informe Integral de Auditoría: Errores, Bugs y Puntos de Falla en el Sistema AGUAS

**Fecha de Auditoría:** 21 de Septiembre de 2026  
**Sistema:** Sistema de Gestión y Control de Planta Potabilizadora y Calidad de Agua (AGUAS)  
**Tecnologías:** PHP 8.3, Laravel 11, Blade, Tailwind CSS, SweetAlert2, MySQL / MariaDB  

---

## 📋 Resumen Ejecutivo

Durante la auditoría exhaustiva realizada sobre el código fuente, la lógica de controladores, la base de datos MySQL y las interfaces de usuario (Blade + JavaScript), se detectaron **11 incidencias técnicas**. Entre ellas se incluyen fallos de integridad referencial, bloqueos en operaciones de guardado, inconsistencias en nombres de campos de formularios, formularios con creación propensa a errores, y posibles excepciones de sintaxis en JavaScript causadas por mensajes de sesión no escapados.

---

## 🚦 Matriz de Severidad

| ID | Módulo / Componente | Descripción Resumida | Severidad | Impacto |
|---|---|---|---|---|
| **BUG-01** | ABM Dinámico (`AbmController`) | Fallo fatal al desmarcar casillas booleanas (`Column cannot be null`) | 🔴 Crítico | Error 500 / Bloqueo en actualización de registros con booleanos |
| **BUG-02** | Operadores (`OperadoresController`) | Bloqueo al registrar 0% en tanques químicos vacíos o sin historial | 🔴 Alto | Impide cargar niveles de tanques en 0% |
| **BUG-03** | Operadores (`operadores/index.blade.php`) | Inconsistencia de nombres en validación de químicos (acordeón no abre) | 🔴 Alto | El usuario no ve los errores de validación de químicos |
| **BUG-04** | Químico (`quimico/index.blade.php`) | Las alertas de caudalímetro no se muestran (Flash Key Mismatch) | 🔴 Alto | Falta de feedback al guardar o borrar caudalímetros |
| **BUG-05** | Global (Vistas Blade + JS) | Inyección de mensajes de sesión sin escapar en SweetAlert2 (`SyntaxError`) | 🔴 Alto | Si un error contiene comillas dobles, rompe todo el JavaScript de la página |
| **BUG-06** | ABM Dinámico (`AbmController`) | `destroyColumn` no bloquea claves primarias ni timestamps en backend | 🟡 Medio | Riesgo de eliminación accidental o maliciosa de columnas críticas (`id`, etc.) |
| **BUG-07** | ABM Dinámico (`table.blade.php`) | Generación de formulario de borrado de columnas mediante `innerHTML` dinámico | 🟡 Medio | Fragilidad ante fallos previos de script |
| **BUG-08** | Jefatura (`jefatura/index.blade.php`) | Gráficos Chart.js renderizados dentro de acordeones cerrados | 🟡 Medio | Gráficos distorsionados o con altura 0 al abrir el acordeón |
| **BUG-09** | Frontend (Paginadores JS) | División por cero en paginadores cuando la tabla no tiene registros | 🟡 Medio | Muestra "Página 0 / 0" y falla la navegación de páginas |
| **BUG-10** | Jefatura (`JefaturaController`) | `rechazarUsuario` sin bloque try-catch ante fallos de Foreign Keys | 🟡 Medio | Posible Error 500 no controlado si el usuario tiene registros vinculados |
| **BUG-11** | Menú Principal (`menu.blade.php`) | Codificación Base64 en tiempo real de imagen de fondo | 🟢 Bajo | Consumo innecesario de CPU y memoria en cada petición al menú |

---

## 🔍 Detalle Técnico de Errores y Soluciones Propuestas

---

### 🔴 BUG-01: Fallo fatal en ABM al desmarcar casillas booleanas (`NOT NULL`)

* **Ubicación:**  
  * Controlador: `app/Http/Controllers/AbmController.php` (Líneas 118-129 y 173-186)  
  * Vista: `resources/views/jefatura/abm/table.blade.php` (Líneas 203-206)
* **Descripción del Problema:**  
  En los navegadores web, los checkboxes que no están marcados no se envían en la petición HTTP POST/PUT. En el método `update()` de `AbmController`:
  ```php
  foreach ($columns as $column) {
      if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) continue;

      if (array_key_exists($column, $data)) {
          $updateData[$column] = $data[$column];
      } else {
          $updateData[$column] = null; // <-- ASIGNA NULL A CAMPOS NO ENVIADOS
      }
  }
  ```
  En tablas como `lab_pozos` (`activo`), `lab_mediciones` (`activo`), `estado_bombas` (`estado`), `users` (`is_approved`), las columnas booleanas están definidas como `TINYINT(1) NOT NULL`. Al desmarcar la casilla para desactivar un registro, el controlador intenta insertar `null`, provocando la excepción:
  ```sql
  SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'activo' cannot be null
  ```
  Asimismo, en el método `store()`, el código ignora los checkboxes no enviados y MySQL aplica el valor por defecto (`1` = activo), ignorando la decisión del usuario si deseaba crearlo inactivo.
* **Solución Propuesta:**  
  Identificar dinámicamente si el tipo de columna en la base de datos es booleano (`tinyint(1)` o `bool`). Si no viene en `$data`, asignar `0` en vez de `null`:
  ```php
  // En store() y update():
  $isBooleanCol = str_contains(strtolower($columnTypes[$column] ?? ''), 'tinyint(1)') 
               || str_contains(strtolower($columnTypes[$column] ?? ''), 'bool');

  if (array_key_exists($column, $data)) {
      $updateData[$column] = $data[$column];
  } else {
      $updateData[$column] = $isBooleanCol ? 0 : null;
  }
  ```

---

### 🔴 BUG-02: Bloqueo al registrar 0% en tanques químicos sin registros previos

* **Ubicación:** `app/Http/Controllers/OperadoresController.php` (Líneas 189-202)
* **Descripción del Problema:**  
  Al momento de validar que el nivel ingresado no sea idéntico al último valor registrado:
  ```php
  $ultimoPrincipal = NivelQuimico::where('quimico', $quimico)
      ->where('tipo_tanque', 'principal')
      ->latest()
      ->value('nivel');

  if ((float)$principal === (float)$ultimoPrincipal) {
      $errores["{$quimico}_principal"] = 'No se puede ingresar el mismo porcentaje actual.';
  }
  ```
  Cuando no existen registros previos para un químico (o el valor es nulo), `$ultimoPrincipal` es `null`.  
  En PHP:
  ```php
  (float)"0" === (float)null // 0.0 === 0.0 -> evalúa a TRUE
  ```
  Si un operador ingresa `0%` para un tanque vacío o sin registros, el sistema interpreta que ingresó el mismo porcentaje y lanza una excepción de validación que bloquea el formulario.
* **Solución Propuesta:**  
  Verificar explícitamente que `$ultimoPrincipal` no sea nulo antes de comparar:
  ```php
  if (!is_null($ultimoPrincipal) && (float)$principal === (float)$ultimoPrincipal) {
      $errores["{$quimico}_principal"] = 'No se puede ingresar el mismo porcentaje actual.';
  }
  ```
  Aplicar la misma corrección para `$auxiliar`.

---

### 🔴 BUG-03: Inconsistencia en nombres de campos de Tanques Químicos (Operadores)

* **Ubicación:** `resources/views/operadores/index.blade.php` (Líneas 107 y 436-447)
* **Descripción del Problema:**  
  El formulario y el controlador utilizan los nombres reales de los campos:  
  `cloro_principal`, `cloro_auxiliar`, `poliamina_principal`, `poliamina_auxiliar`, `sulfato_principal`, `sulfato_auxiliar`.  
  Sin embargo, la vista Blade comprueba nombres heredados de una versión anterior:
  ```blade
  $erroresQuimicos = $errors->hasAny(['quimico', 'tanque_principal', 'tanque_auxiliar']) 
                     || session('success_quimicos') || session('error_quimicos');
  ```
  Y dentro del acordeón en la línea 442:
  ```blade
  @foreach(['quimico', 'tanque_principal', 'tanque_auxiliar'] as $field)
      @error($field)
          <li>{{ $message }}</li>
      @enderror
  @endforeach
  ```
* **Impacto:**  
  Si la validación falla (ej. valor no numérico, mayor a 100%, o duplicado), `$erroresQuimicos` es `false`. El acordeón **permanece cerrado** y los mensajes específicos nunca se imprimen dentro de la sección, dejando al operador desconcertado.
* **Solución Propuesta:**  
  Reemplazar el arreglo de campos por los nombres reales:
  ```blade
  @php
      $camposQuimicos = ['cloro_principal', 'cloro_auxiliar', 'poliamina_principal', 'poliamina_auxiliar', 'sulfato_principal', 'sulfato_auxiliar'];
      $erroresQuimicos = $errors->hasAny($camposQuimicos) || session('success_quimicos') || session('error_quimicos');
  @endphp
  ```

---

### 🔴 BUG-04: Silenciamiento de alertas de Caudalímetro en Módulo Químico

* **Ubicación:**  
  * Controlador: `app/Http/Controllers/QuimicoController.php` (Líneas 306, 315, 319, 324)  
  * Vista: `resources/views/quimico/index.blade.php` (Líneas 83-93 y 760-780)
* **Descripción del Problema:**  
  Al guardar o eliminar una lectura de caudalímetro, el controlador emite:
  ```php
  return back()->with('success_caudal', 'Lectura de caudalímetro registrada.');
  return back()->with('error_caudal', 'No tienes permisos para borrar este registro.');
  ```
  Sin embargo, `quimico/index.blade.php` solo comprueba:
  ```blade
  @elseif(session('success'))
  @elseif(session('deleted'))
  @elseif(session('error'))
  ```
* **Impacto:**  
  Al registrar o eliminar caudalímetros, la página se recarga pero SweetAlert2 no muestra ningún mensaje de confirmación ni de error.
* **Solución Propuesta:**  
  Incorporar `session('success_caudal')` y `session('error_caudal')` en el bloque de alertas de SweetAlert2:
  ```blade
  @elseif(session('success') || session('success_caudal'))
      SwalCustom.fire({
          title: '¡Guardado!',
          text: {!! json_encode(session('success') ?? session('success_caudal')) !!},
          icon: 'success',
          confirmButtonText: 'Aceptar'
      });
  @elseif(session('error') || session('error_caudal'))
      SwalCustom.fire({
          title: '¡Error!',
          text: {!! json_encode(session('error') ?? session('error_caudal')) !!},
          icon: 'error',
          confirmButtonText: 'Cerrar'
      });
  ```

---

### 🔴 BUG-05: Inyección insegura de cadenas de sesión en JavaScript (`SyntaxError`)

* **Ubicación:**  
  * `resources/views/jefatura/index.blade.php` (Líneas 1037, 1045, 1051)  
  * `resources/views/jefatura/abm/table.blade.php` (Líneas 445, 452, 459)  
  * `resources/views/operadores/index.blade.php` (Líneas 713, 720)  
  * `resources/views/quimico/index.blade.php` (Líneas 763, 769, 776)  
  * `resources/views/laboratorio/index.blade.php` (Líneas 1253, 1260, 1267)
* **Descripción del Problema:**  
  En todas las vistas, los textos de sesión se interpolan dentro de cadenas de texto con comillas dobles en JavaScript:
  ```javascript
  text: "{{ session('error') }}"
  ```
  Si el mensaje de error proviene de una excepción de base de datos o contiene comillas dobles (ej: `Error en la tabla "users": registro no encontrado`), el HTML resultante es:
  ```javascript
  text: "Error en la tabla "users": registro no encontrado"
  ```
  Esto produce de inmediato un error fatal de JavaScript:
  ```
  Uncaught SyntaxError: Unexpected identifier 'users'
  ```
* **Impacto:**  
  La ejecución de todo el script de la página se detiene por completo: no funcionan los modales, no funcionan las confirmaciones de borrado, no funcionan los acordeones y se interrumpe el polling en tiempo real de bombas.
* **Solución Propuesta:**  
  Reemplazar la interpolación simple por `{!! json_encode(...) !!}`, lo cual escapa automáticamente comillas, saltos de línea y caracteres especiales cumpliendo con el estándar JSON/JS:
  ```javascript
  text: {!! json_encode(session('error')) !!}
  ```

---

### 🟡 BUG-06: `destroyColumn` no protege columnas del sistema en backend

* **Ubicación:** `app/Http/Controllers/AbmController.php` (Líneas 309-334)
* **Descripción del Problema:**  
  La interfaz gráfica oculta los botones de eliminación para `['id', 'created_at', 'updated_at', 'deleted_at']`. No obstante, el método `destroyColumn()` en el controlador no valida si la columna solicitada es una de las columnas reservadas o clave primaria.  
  Si una tabla no tiene registros, cualquier usuario con rol jefatura/admin puede enviar una petición manual `DELETE /jefatura/abm/{table}/column/id` y el controlador ejecutará:
  ```php
  Schema::table($table, function (Blueprint $t) use ($column) {
      $t->dropColumn($column);
  });
  ```
* **Impacto:**  
  Eliminación accidental o forzada de claves primarias (`id`), rompiendo la integridad del esquema y los modelos Eloquent correspondientes.
* **Solución Propuesta:**  
  Validar en el backend antes de proceder con el drop:
  ```php
  $protectedColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
  if (in_array(strtolower($column), $protectedColumns)) {
      return redirect()->back()->with('error', "No se puede eliminar la columna protegida '{$column}'.");
  }
  ```

---

### 🟡 BUG-07: Formulario dinámico de eliminación de columnas mediante `innerHTML`

* **Ubicación:** `resources/views/jefatura/abm/table.blade.php` (Líneas 415-420)
* **Descripción del Problema:**  
  El formulario oculto para eliminar columnas se construye dinámicamente mediante manipulación de DOM en JS:
  ```javascript
  const deleteColumnForm = document.createElement('form');
  deleteColumnForm.method = 'POST';
  deleteColumnForm.id = 'delete-column-form';
  deleteColumnForm.style.display = 'none';
  deleteColumnForm.innerHTML = `@csrf @method('DELETE')`;
  document.body.appendChild(deleteColumnForm);
  ```
  Si se produce algún error de JavaScript antes de esta línea, el formulario nunca llega a crearse en el DOM y la acción `confirmarEliminarColumna` falla con `Cannot read properties of null (reading 'action')`.
* **Solución Propuesta:**  
  Declarar el formulario de forma estática en el cuerpo del HTML de Blade (como en todos los demás módulos):
  ```blade
  <form id="delete-column-form" method="POST" style="display: none;">
      @csrf
      @method('DELETE')
  </form>
  ```

---

### 🟡 BUG-08: Distorsión de gráficos Chart.js dentro de acordeones cerrados

* **Ubicación:** `resources/views/jefatura/index.blade.php` (Líneas 312, 325, 374, 407)
* **Descripción del Problema:**  
  Los lienzos `<canvas>` de Chart.js están encerrados en elementos `<details>` que arrancan cerrados. Cuando Chart.js inicializa un gráfico en un contenedor oculto con `display: none`, las propiedades `offsetWidth` y `offsetHeight` son `0`. Al abrir el acordeón por primera vez, el gráfico puede renderizarse plano, sin altura o visiblemente distorsionado.
* **Solución Propuesta:**  
  En el listener del evento `toggle` de los elementos `<details>` en Jefatura, despachar un evento de resize para que Chart.js redibuje el canvas con sus dimensiones reales:
  ```javascript
  detail.addEventListener('toggle', () => {
      if (detail.open) {
          window.dispatchEvent(new Event('resize'));
      }
  });
  ```

---

### 🟡 BUG-09: Paginador frontend muestra "Página 0 / 0" en tablas vacías

* **Ubicación:**  
  * `resources/views/operadores/index.blade.php` (Línea 731)  
  * `resources/views/quimico/index.blade.php` (Líneas 787 y 818)  
  * `resources/views/laboratorio/index.blade.php` (Línea 1298)
* **Descripción del Problema:**  
  Se calcula la cantidad de páginas con `Math.ceil(totalItems / itemsPerPage)`. Cuando no hay registros (`totalItems = 0`):
  `Math.ceil(0 / 8)` resulta en `0`.  
  El indicador muestra `Página 1 / 0` o `Página 0 / 0`, y al presionar las flechas de navegación los cálculos de página actual generan comportamientos erráticos.
* **Solución Propuesta:**  
  Asegurar un valor mínimo de 1 página:
  ```javascript
  const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));
  ```

---

### 🟡 BUG-10: `rechazarUsuario` sin captura de excepciones de integridad referencial

* **Ubicación:** `app/Http/Controllers/JefaturaController.php` (Líneas 142-154)
* **Descripción del Problema:**  
  Al rechazar un usuario, se ejecuta `$user->forceDelete()`. Si el usuario no aprobado llegó a generar datos asociados en tablas con restricción de clave externa (`restrictOnDelete`), la base de datos lanza una excepción no controlada (`QueryException 1451`), resultando en una pantalla de error 500 para el administrador.
* **Solución Propuesta:**  
  Envolver la llamada en un bloque `try / catch`:
  ```php
  try {
      $user->forceDelete();
      return redirect()->route('jefatura.index')->with('success', "El usuario {$nombre} ha sido rechazado y eliminado.");
  } catch (\Illuminate\Database\QueryException $e) {
      return redirect()->route('jefatura.index')->with('error', "No se puede eliminar al usuario {$nombre} porque posee registros asociados.");
  }
  ```

---

### 🟢 BUG-11: Codificación innecesaria de imagen en Base64 en `menu.blade.php`

* **Ubicación:** `resources/views/menu.blade.php` (Línea 57)
* **Descripción del Problema:**  
  La vista del menú principal lee el archivo de imagen del disco y lo codifica en Base64 en cada petición:
  ```blade
  style="background-image: url('data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path("img/fondo-ciudad.jpg"))) }}');"
  ```
  Esto incrementa el tamaño del HTML en cientos de kilobytes, consume ciclos de CPU innecesarios y anula la memoria caché del navegador para imágenes estáticas.
* **Solución Propuesta:**  
  Utilizar el helper de assets estándar:
  ```blade
  style="background-image: url('{{ asset('img/fondo-ciudad.jpg') }}');"
  ```

---

## 🛠️ Plan de Implementación Recomendado

1. **Fase 1: Estabilidad de Base de Datos y ABM (BUG-01 y BUG-06)**  
   Modificar `AbmController.php` para contemplar columnas booleanas en `store()` y `update()`, y bloquear la eliminación de columnas reservadas en `destroyColumn()`.

2. **Fase 2: Robustez de Scripts y Seguridad de Sesión (BUG-05 y BUG-07)**  
   Reemplazar las interpolaciones simples `{{ session(...) }}` por `{!! json_encode(session(...)) !!}` en todas las vistas Blade para evitar roturas por comillas, y declarar el formulario estático en `table.blade.php`.

3. **Fase 3: Sincronización de Campos y Mensajes de Módulos (BUG-02, BUG-03, BUG-04 y BUG-10)**  
   * Corregir el condicional de valor 0% en `OperadoresController.php`.
   * Corregir los nombres de campos de químicos en `operadores/index.blade.php`.
   * Integrar las claves de caudalímetro en `quimico/index.blade.php`.
   * Añadir control de excepciones en `JefaturaController@rechazarUsuario`.

4. **Fase 4: Pulido de UX y Rendimiento (BUG-08, BUG-09 y BUG-11)**  
   * Agregar el re-disparo de resize para Chart.js en `jefatura/index.blade.php`.
   * Proteger el cálculo de páginas frontend contra división por cero.
   * Reemplazar la imagen en base64 de `menu.blade.php` por `asset()`.

---
*Informe generado por Antigravity AI Coding Assistant.*
