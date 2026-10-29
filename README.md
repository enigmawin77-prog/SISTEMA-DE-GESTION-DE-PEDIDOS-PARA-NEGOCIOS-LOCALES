# SABORLOCAL - SISTEMA DE GESTIÓN DE PEDIDOS PARA NEGOCIOS LOCALES

Aplicación web profesional full stack desarrollada en **PHP 8.2**, **MySQL (MariaDB)**, **PDO**, **Bootstrap 5**, **Chart.js** y **JavaScript**, diseñada para la administración integral de pedidos, inventario, clientes, pagos y repartos en negocios locales (restaurantes, cafeterías, panaderías, bodegas y tiendas).

---

## 1. Tecnologías Utilizadas

- **Servidor Web:** Apache (vía XAMPP).
- **Lenguaje:** PHP 8.2 o superior (Compatible con PHP 8.3+).
- **Base de Datos:** MySQL / MariaDB mediante PDO con consultas preparadas y transacciones ACID.
- **Frontend:** HTML5 semántico, CSS3 personalizado, Bootstrap 5.3.3, Bootstrap Icons 1.11.3.
- **Gráficos e Interactividad:** Chart.js 4, Fetch API y SweetAlert2.
- **Seguridad:** Tokens CSRF, `password_hash()` con BCRYPT, sesiones seguras (`HttpOnly`, `SameSite=Lax`), prevención de XSS y protección contra inyección SQL.

---

## 2. Credenciales de Acceso Inicial

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@saborlocal.pe` | `admin123` |
| **Empleado** | `empleado@saborlocal.pe` | `empleado123` |

---

## 3. Estructura de Carpetas del Proyecto

```text
C:\xampp\htdocs\gestion-pedidos-locales\
│
├── index.php                      # Front Controller principal y autocargador PSR-4
├── login.php                      # Acceso directo al login
├── logout.php                     # Acceso directo para cierre de sesión
├── dashboard.php                  # Acceso directo al panel administrativo
├── .htaccess                      # Seguridad de Apache y reescritura de URLs
├── README.md                      # Manual completo de instalación y arquitectura
│
├── config/
│   ├── app.php                    # Configuración general (nombre, timezone, sesiones)
│   └── database.php               # Credenciales y opciones de conexión PDO
│
├── database/
│   └── database.sql               # Esquema relacional completo y datos de demostración
│
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductoController.php
│   │   ├── CategoriaController.php
│   │   ├── ClienteController.php
│   │   ├── PedidoController.php
│   │   ├── PagoController.php
│   │   ├── EntregaController.php
│   │   ├── ReporteController.php
│   │   └── ConfiguracionController.php
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Categoria.php
│   │   ├── Producto.php
│   │   ├── Cliente.php
│   │   ├── Pedido.php
│   │   ├── Pago.php
│   │   ├── Entrega.php
│   │   └── Configuracion.php
│   ├── Services/
│   │   └── ReporteService.php
│   ├── Helpers/
│   │   ├── Database.php
│   │   ├── AuthHelper.php
│   │   ├── CsrfHelper.php
│   │   ├── FormatHelper.php
│   │   ├── FlashHelper.php
│   │   └── UploadHelper.php
│   └── Validators/
│       └── Validator.php
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css          # Estilos visuales empresariales premium
│   │   └── js/
│   │       └── main.js            # Lógica de gráficos Chart.js y UI
│   └── uploads/
│       └── productos/             # Almacenamiento seguro de imágenes
│
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   ├── navbar.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── login.php
│   │   └── perfil.php
│   ├── dashboard/
│   │   └── index.php
│   ├── productos/
│   │   └── index.php
│   ├── categorias/
│   │   └── index.php
│   ├── clientes/
│   │   ├── index.php
│   │   └── detalle.php
│   ├── pedidos/
│   │   ├── index.php
│   │   ├── crear.php
│   │   ├── detalle.php
│   │   └── comprobante.php
│   ├── pagos/
│   │   └── index.php
│   ├── entregas/
│   │   └── index.php
│   ├── reportes/
│   │   └── index.php
│   ├── configuracion/
│   │   └── index.php
│   └── errors/
│       └── 404.php
│
├── routes/
│   └── web.php                    # Despachador central de rutas
│
└── storage/
    └── logs/
        └── app.log                # Registro privado de errores del sistema
```

---

## 4. Guía de Instalación en XAMPP

1. **Abrir el Panel de Control de XAMPP:**
   - Inicie los módulos **Apache** y **MySQL** asegurándose de que ambos muestren estado verde.
2. **Crear o Vincular la Carpeta del Proyecto:**
   - Ubique la carpeta del proyecto en `C:\xampp\htdocs\gestion-pedidos-locales`.
3. **Importar la Base de Datos desde phpMyAdmin:**
   - Abra en su navegador web: `http://localhost/phpmyadmin/`.
   - Seleccione la pestaña **Importar**.
   - Haga clic en **Seleccionar archivo** y elija: `C:\xampp\htdocs\gestion-pedidos-locales\database\database.sql`.
   - Haga clic en **Importar** al final de la página. La base de datos `gestion_pedidos_locales` y sus 10 tablas se crearán automáticamente con datos demostrativos.
4. **Verificar la Conexión PDO:**
   - Abra el archivo `config/database.php`.
   - Por defecto, en XAMPP el usuario es `root` sin contraseña (`''`) y el puerto es `3306`.
5. **Ejecutar la Aplicación Web:**
   - Ingrese desde su navegador a:
     `http://localhost/gestion-pedidos-locales/`
   - Inicie sesión con: `admin@saborlocal.pe` / `admin123`.

---

## 5. Módulos y Reglas de Negocio Implementadas

### A. Dashboard Ejecutivo en Tiempo Real
- **Métricas:** Total de pedidos, pedidos pendientes, en preparación, entregados, cancelados, ventas del día (S/), ventas del mes (S/), productos registrados y alertas de stock bajo.
- **Gráficos Chart.js:** Curva de ventas por día con gradiente suave y dona de pedidos por estado con leyenda porcentual.
- **Listados de Alta Fidelidad:** Pedidos recientes y ranking de productos más vendidos con imágenes y unidades vendidas.

### B. Gestión de Pedidos y Punto de Venta (POS)
- Catálogo interactivo de productos con filtrado en tiempo real por categoría y texto.
- Canasta con cálculo instantáneo de subtotales, descuentos y costo de envío según la modalidad.
- Modalidad **Domicilio** (con dirección, referencia y chofer asignado) vs. **Recojo en Tienda**.
- Validación estricta de stock en servidor antes de confirmar.
- Registro transaccional seguro con rollback automático ante cualquier fallo.

### C. Control de Estados e Inventario
- Estados: `pendiente` ➜ `confirmado` ➜ `en_preparacion` ➜ `listo` ➜ `en_camino` ➜ `entregado` (o `cancelado`).
- **Descuento de Stock:** Se descuenta al confirmar o crear el pedido.
- **Devolución de Stock:** Si un pedido se cancela, el sistema devuelve automáticamente las cantidades al inventario una sola vez.
- **Bloqueo:** No se permite editar pedidos entregados ni reactivar pedidos cancelados.

### D. Cobranzas y Pagos Parciales
- Soporta efectivo, Yape, Plin, transferencia bancaria y tarjetas.
- Control de saldo pendiente y pagos fraccionados.
- Solo marca como `pagado` cuando el importe recibido cubre el total de la orden.

### E. Comprobante Interno Imprimible
- Formato térmico tipo ticket estándar de 80mm con datos del negocio, desglose de ítems, totales, pagos y leyenda aclaratoria de comprobante interno sin valor tributario.

### F. Reportes y Exportación Excel
- Filtros por fechas, estados, clientes y modalidades.
- Exportación instantánea a **CSV compatible con Microsoft Excel** con codificación UTF-8 BOM.
