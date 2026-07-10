# 🍰 Postres Laura — Sistema de Gestión
 FALTA SUBIR EL ARCHIVO VENDOR AL REPOSITORIO 
 The vendor file still needs to be uploaded to the repository.

Sistema web desarrollado en **Laravel 11** para la gestión integral de un emprendimiento de repostería: inventario, ventas, gastos, empleados y reportes exportables. Incluye componente **Kotlin** para estadísticas avanzadas.

---

## 🚀 Instalación

```bash
# 1. Clonar / copiar el proyecto
cd postres

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
copy .env.example .env
php artisan key:generate

# 4. Crear base de datos MySQL llamada "postres"
# Configurar DB_USERNAME y DB_PASSWORD en .env si es necesario

# 5. Migrar y poblar datos de prueba
php artisan migrate --seed

# 6. Iniciar servidor de desarrollo
php artisan serve
# → http://localhost:8000
```

## 🧑‍💻 Usuarios de prueba

| Correo | Contraseña | Rol |
|--------|-----------|-----|
| laura@postres.com | laura123 | Emprendedor (admin) |
| david@postres.com | david456 | Empleado |
| maria@email.com   | maria789 | Cliente |

---

## 🔌 API REST — Endpoints

```
GET    /api/v1/productos          Lista todos los productos
GET    /api/v1/productos/{id}     Detalle de un producto
POST   /api/v1/productos          Crear producto
PUT    /api/v1/productos/{id}     Actualizar producto
DELETE /api/v1/productos/{id}     Eliminar producto

GET    /api/v1/usuarios           Lista usuarios
POST   /api/v1/usuarios           Crear usuario
PUT    /api/v1/usuarios/{id}      Actualizar usuario
DELETE /api/v1/usuarios/{id}      Eliminar usuario

GET    /api/v1/ventas             Lista ventas
POST   /api/v1/ventas             Registrar venta

GET    /api/v1/compras            Lista gastos/compras
POST   /api/v1/compras            Registrar egreso
DELETE /api/v1/compras/{id}       Eliminar gasto

GET    /api/v1/stats              KPIs del sistema
GET    /api/v1/kotlin/stats       Estadísticas calculadas por Kotlin
GET    /api/v1/kotlin/health      Estado del servicio Kotlin
```

---

## 🧪 Componente Kotlin( por el momento no se va disponer de un componente kotlin, por lo tanto se debera ingnorar esto)

Microservicio independiente que calcula estadísticas avanzadas de ventas consultando la BD MySQL y las expone en JSON para ser consumidas por Laravel.

```bash
# Compilar (requiere kotlinc + mysql-connector-java en classpath)
kotlinc kotlin/StatsService.kt -include-runtime -d kotlin/stats.jar

# Ejecutar (puerto 8081)
java -jar kotlin/stats.jar

# Verificar desde Laravel
curl http://localhost:8000/api/v1/kotlin/stats
```

---

## 🗂️ Estructura del proyecto

```
postres/
├── app/
│   ├── Http/Controllers/        # Controladores web
│   │   └── Api/                 # Controladores API REST
│   ├── Http/Middleware/         # AuthSession + CheckRole
│   └── Models/                  # Eloquent ORM
├── database/
│   ├── migrations/              # Esquema de BD
│   └── seeders/                 # Datos de prueba
├── kotlin/
│   └── StatsService.kt          # Microservicio Kotlin
├── resources/views/             # Plantillas Blade
├── routes/
│   ├── web.php                  # Rutas web con middleware
│   └── api.php                  # API REST
└── public/css/app.css           # Estilos personalizados
```

---

## 📋 Control de Versiones (Git)

```bash
git init
git add .
git commit -m "feat: inicializar proyecto Laravel Postres Laura SENA 228118"
git commit -m "feat: autenticación y gestión de sesiones por roles"
git commit -m "feat: CRUD completo de productos, usuarios y gastos"
git commit -m "feat: API REST endpoints con GET POST PUT DELETE"
git commit -m "feat: reportes PDF y exportación CSV/Excel"
git commit -m "feat: integración componente Kotlin StatsService"
git commit -m "feat: vistas Blade completas - dashboard, empleados, reportes"
git commit -m "fix: migraciones, seeders y archivos de arranque Laravel 11"
```

---

**Tecnologías:** PHP 8.2 · Laravel 11 · MySQL · Kotlin · DomPDF · Blade
**SENA · Centro de Servicios Financieros · Regional Distrito Capital**
