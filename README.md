# 💧 AGUAS - Sistema Integral de Planta Potabilizadora

Sistema web integral desarrollado en **Laravel** para la telemetría, gestión operativa, monitoreo de calidad fisicoquímica/bacteriológica y supervisión en tiempo real de una planta potabilizadora de agua.

---

## 📌 Módulos del Sistema

1. **Encargado de Turno (`/operadores`)**:
   - Accionamiento de bombas de captación (Río y Pozos) con enclavamiento de seguridad.
   - Registro de presiones hidráulicas (Bajada de Tanque, Planta, Falcón) y nivel de cisterna.
   - Registro y control de ciclos de retrolavado de filtros rápidos (Línea Norte y Línea Sur).
   - Monitoreo de porcentaje de reactivos químicos (Cloro, Poliamina, Sulfato).
   - Libro de guardia / novedades por turno.

2. **Técnico Químico (`/quimico`)**:
   - Telemetría en tiempo real de bombas en modo lectura.
   - Ensayos fisicoquímicos en 7 puntos (Río, Decantadores, Filtros, Cisterna, Bajada de Tanque).
   - Ensayos bacteriológicos rápidos (*E. coli* y Coliformes Totales).
   - Registro de caudalímetros de dosificación ($m^3/h$).

3. **Laboratorio Central (`/laboratorio`)**:
   - Arquitectura flexible basada en el patrón **EAV (Entity-Attribute-Value)**.
   - Ensayos de calidad en recepción de insumos químicos (Sulfato, Hipoclorito, Poliamina, Cal).
   - Ensayos completos fisicoquímicos, toxicológicos y biológicos en Agua Cruda y Producto Terminado.
   - Análisis microbiológico de pozos de extracción subterránea.

4. **Jefatura y Dirección (`/jefatura`)**:
   - Control de acceso y aprobación jerárquica de usuarios registrados.
   - Gestión de empleados y reasignación de roles RBAC.
   - Dashboards analíticos e interactivos con **Chart.js** (curvas de turbiedad, pH, cloro, presiones y lavado de filtros).
   - Reportes históricos paginados con filtros multidimensionales.

---

## 🚀 Puesta en Marcha Rápida

### Requisitos:
- PHP 8.3+ con extensiones requeridas de Laravel (`curl`, `mbstring`, `pdo_mysql`, `bcmath`, `xml`).
- Composer 2+
- MySQL 8.0+ / MariaDB 10.6+
- Node.js 20+ y NPM

### Instalación:
```bash
# 1. Clonar el repositorio
git clone https://github.com/titotroffe/AGUAS.git
cd AGUAS

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env y migrar
php artisan migrate --force
php artisan db:seed --class=EavSeeder

# 5. Compilar assets y levantar servidor
npm run build
php artisan serve
```

---

## 📖 Documentación Completa

Para acceder a la especificación técnica exhaustiva, diagrama de arquitectura, modelo relacional de base de datos (ERD), matriz RBAC, catálogo de endpoints y manual de usuario por puesto, consulte el documento:

👉 **[DOCUMENTACION.md](DOCUMENTACION.md)**

---

## 🛡️ Seguridad y Buenas Prácticas
Consulte los reportes de auditoría y análisis de calidad de software en `/pentesting/aguas/` para el plan de remediación antes de despliegues en entornos de producción.

