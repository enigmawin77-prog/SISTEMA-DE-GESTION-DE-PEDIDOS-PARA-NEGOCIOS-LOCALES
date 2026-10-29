-- ============================================================================
-- BASE DE DATOS: GESTIÓN DE PEDIDOS PARA NEGOCIOS LOCALES
-- Compatible con MySQL 8+ y MariaDB 10.4+ en XAMPP
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `gestion_pedidos_locales`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gestion_pedidos_locales`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `historial_estados_pedido`;
DROP TABLE IF EXISTS `entregas`;
DROP TABLE IF EXISTS `pagos`;
DROP TABLE IF EXISTS `detalle_pedidos`;
DROP TABLE IF EXISTS `pedidos`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `configuracion_negocio`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. TABLA: configuracion_negocio
-- ----------------------------------------------------------------------------
CREATE TABLE `configuracion_negocio` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_comercial` VARCHAR(150) NOT NULL DEFAULT 'Restaurante El Buen Sabor',
  `ruc` VARCHAR(20) DEFAULT '20601234567',
  `razon_social` VARCHAR(200) DEFAULT 'El Buen Sabor S.A.C.',
  `direccion` VARCHAR(255) DEFAULT 'Av. Las Palmeras 123, Miraflores, Lima',
  `telefono` VARCHAR(50) DEFAULT '+51 987 654 321',
  `email` VARCHAR(150) DEFAULT 'contacto@elbuensabor.pe',
  `logo` VARCHAR(255) DEFAULT 'logo.png',
  `moneda_simbolo` VARCHAR(10) NOT NULL DEFAULT 'S/',
  `moneda_codigo` VARCHAR(10) NOT NULL DEFAULT 'PEN',
  `igv_porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 18.00,
  `costo_envio_predeterminado` DECIMAL(10,2) NOT NULL DEFAULT 5.00,
  `mensaje_ticket` TEXT DEFAULT '¡Gracias por su preferencia! Comprobante interno sin valor tributario.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. TABLA: usuarios
-- ----------------------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'empleado') NOT NULL DEFAULT 'empleado',
  `telefono` VARCHAR(30) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_usuario_email` (`email`),
  INDEX `idx_usuario_rol` (`rol`),
  INDEX `idx_usuario_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. TABLA: categorias
-- ----------------------------------------------------------------------------
CREATE TABLE `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `descripcion` TEXT DEFAULT NULL,
  `icono` VARCHAR(50) DEFAULT 'bi-tag',
  `color` VARCHAR(20) DEFAULT '#0d6efd',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_categoria_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. TABLA: productos
-- ----------------------------------------------------------------------------
CREATE TABLE `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL UNIQUE,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `categoria_id` INT NOT NULL,
  `precio_venta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `costo_compra` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `stock_minimo` INT NOT NULL DEFAULT 5,
  `imagen` VARCHAR(255) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_producto_categoria` (`categoria_id`),
  INDEX `idx_producto_nombre` (`nombre`),
  INDEX `idx_producto_activo` (`activo`),
  INDEX `idx_producto_stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. TABLA: clientes
-- ----------------------------------------------------------------------------
CREATE TABLE `clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo_documento` ENUM('DNI', 'RUC', 'CE', 'OTRO') DEFAULT 'DNI',
  `numero_documento` VARCHAR(20) DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `direccion` TEXT DEFAULT NULL,
  `referencia` TEXT DEFAULT NULL,
  `notas` TEXT DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_cliente_nombre` (`nombre`),
  INDEX `idx_cliente_documento` (`numero_documento`),
  INDEX `idx_cliente_telefono` (`telefono`),
  INDEX `idx_cliente_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. TABLA: pedidos
-- ----------------------------------------------------------------------------
CREATE TABLE `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `numero_pedido` VARCHAR(30) NOT NULL UNIQUE,
  `cliente_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `modalidad` ENUM('recojo', 'domicilio') NOT NULL DEFAULT 'domicilio',
  `estado` ENUM('pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `descuento` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `costo_envio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notas` TEXT DEFAULT NULL,
  `estado_pago` ENUM('pendiente', 'parcial', 'pagado') NOT NULL DEFAULT 'pendiente',
  `monto_pagado` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `saldo_pendiente` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `direccion_entrega` TEXT DEFAULT NULL,
  `fecha_pedido` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_pedidos_estado` (`estado`),
  INDEX `idx_pedidos_modalidad` (`modalidad`),
  INDEX `idx_pedidos_estado_pago` (`estado_pago`),
  INDEX `idx_pedidos_fecha` (`fecha_pedido`),
  INDEX `idx_pedidos_cliente` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. TABLA: detalle_pedidos
-- ----------------------------------------------------------------------------
CREATE TABLE `detalle_pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `notas` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_detalle_pedido` (`pedido_id`),
  INDEX `idx_detalle_producto` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. TABLA: pagos
-- ----------------------------------------------------------------------------
CREATE TABLE `pagos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `metodo_pago` ENUM('efectivo', 'yape', 'plin', 'transferencia', 'tarjeta', 'otro') NOT NULL DEFAULT 'efectivo',
  `monto` DECIMAL(10,2) NOT NULL,
  `referencia` VARCHAR(100) DEFAULT NULL,
  `comprobante` VARCHAR(255) DEFAULT NULL,
  `notas` TEXT DEFAULT NULL,
  `fecha_pago` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pagos_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_pagos_pedido` (`pedido_id`),
  INDEX `idx_pagos_metodo` (`metodo_pago`),
  INDEX `idx_pagos_fecha` (`fecha_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. TABLA: entregas
-- ----------------------------------------------------------------------------
CREATE TABLE `entregas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL UNIQUE,
  `repartidor_nombre` VARCHAR(120) DEFAULT NULL,
  `repartidor_telefono` VARCHAR(30) DEFAULT NULL,
  `direccion` TEXT NOT NULL,
  `referencia` TEXT DEFAULT NULL,
  `estado` ENUM('pendiente', 'en_camino', 'entregado', 'fallido') NOT NULL DEFAULT 'pendiente',
  `fecha_salida` DATETIME DEFAULT NULL,
  `fecha_entrega` DATETIME DEFAULT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_entregas_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_entregas_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. TABLA: historial_estados_pedido
-- ----------------------------------------------------------------------------
CREATE TABLE `historial_estados_pedido` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `estado_anterior` VARCHAR(50) DEFAULT NULL,
  `estado_nuevo` VARCHAR(50) NOT NULL,
  `motivo` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_historial_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_historial_pedido` (`pedido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- DATOS INICIALES Y DE DEMOSTRACIÓN
-- ============================================================================

-- Configuración general del negocio
INSERT INTO `configuracion_negocio` (`id`, `nombre_comercial`, `ruc`, `razon_social`, `direccion`, `telefono`, `email`, `logo`, `moneda_simbolo`, `moneda_codigo`, `igv_porcentaje`, `costo_envio_predeterminado`, `mensaje_ticket`)
VALUES (1, 'Restaurante El Buen Sabor', '20601234567', 'El Buen Sabor S.A.C.', 'Av. Las Palmeras 123, Miraflores, Lima', '+51 987 654 321', 'contacto@elbuensabor.pe', 'logo.png', 'S/', 'PEN', 18.00, 5.00, '¡Gracias por su preferencia! Comprobante interno para control del pedido.');

-- Usuarios iniciales (Clave admin: admin123 / Clave empleado: empleado123)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `telefono`, `avatar`, `activo`, `ultimo_acceso`) VALUES
(1, 'Carlos Mendoza', 'admin@saborlocal.pe', '$2y$10$Im3jzN/YZJLyHAT5hnHQ9O1RQeIZ3IdUcZOC2Xq7Du82Gu.YVXjZ2', 'admin', '987654321', 'avatar.png', 1, NOW()),
(2, 'Lucía Ramos', 'empleado@saborlocal.pe', '$2y$10$FCG7Y83wmO3dXvZ4u26ZDO8pZ4pitMYMAq6oAlAR4Xt8h7uHmzN6a', 'empleado', '912345678', NULL, 1, NOW());

-- Categorías de productos
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `color`, `activo`) VALUES
(1, 'Hamburguesas', 'Hamburguesas artesanales de res y pollo con pan brioche', 'bi-egg-fried', '#f59e0b', 1),
(2, 'Pizzas', 'Pizzas a la leña con masa madre y mozzarella fresca', 'bi-pie-chart', '#ef4444', 1),
(3, 'Bebidas y Refrescos', 'Bebidas frías, jugos naturales, gaseosas y aguas', 'bi-cup-straw', '#3b82f6', 1),
(4, 'Guarniciones y Snacks', 'Papas fritas, alitas, nuggets y acompañamientos crocantes', 'bi-box-seam', '#10b981', 1),
(5, 'Postres y Cafés', 'Tortas caseras, brownies y cafés peruanos selectos', 'bi-cup-hot', '#8b5cf6', 1);

-- Catálogo de productos (48 productos o base inicial rica, con 7 productos en stock bajo)
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `descripcion`, `categoria_id`, `precio_venta`, `costo_compra`, `stock`, `stock_minimo`, `imagen`, `activo`) VALUES
(1, 'HAM-001', 'Hamburguesa Clásica', 'Carne 150g, queso cheddar, lechuga, tomate y salsa especial en pan brioche', 1, 16.50, 8.00, 45, 10, 'burger.jpg', 1),
(2, 'HAM-002', 'Hamburguesa Doble Queso', 'Doble carne 300g, doble queso cheddar fundido, tocino crocante y cebolla caramelizada', 1, 22.00, 11.50, 30, 8, 'burger.jpg', 1),
(3, 'HAM-003', 'Hamburguesa Royal', 'Carne 150g, huevo frito a la plancha, queso edam, lechuga y papas al hilo', 1, 19.50, 9.50, 20, 8, 'burger.jpg', 1),
(4, 'HAM-004', 'Hamburguesa BBQ Tocino', 'Carne 180g, tocino ahumado, salsa BBQ casera y aros de cebolla crocantes', 1, 23.50, 12.00, 4, 10, 'burger.jpg', 1), -- Stock bajo (1)
(5, 'PIZ-001', 'Pizza Familiar Especial', 'Salsa de tomate casera, queso mozzarella abundante, jamón inglés, champiñones y orégano', 2, 42.00, 18.00, 25, 5, 'pizza.jpg', 1),
(6, 'PIZ-002', 'Pizza Mediana Pepperoni', 'Mozzarella fundida, doble pepperoni premium y toque de albahaca', 2, 32.00, 14.00, 18, 5, 'pizza.jpg', 1),
(7, 'PIZ-003', 'Pizza Suprema 4 Quesos', 'Mozzarella, parmesano, gorgonzola y queso crema artesanal', 2, 45.00, 20.00, 3, 5, 'pizza.jpg', 1), -- Stock bajo (2)
(8, 'PIZ-004', 'Pizza Hawaiana Clásica', 'Salsa pomodoro, queso mozzarella, jamón seleccionado y trozos de piña glaseada', 2, 38.00, 16.00, 12, 5, 'pizza.jpg', 1),
(9, 'GUA-001', 'Papas Fritas Medianas', 'Papas nativas amarillas fritas al punto de sal marina', 4, 9.00, 3.50, 50, 15, 'fries.jpg', 1),
(10, 'GUA-002', 'Papas Fritas Rústicas Grandes', 'Papas con cáscara sazonadas con finas hierbas y paprika', 4, 14.00, 5.00, 32, 10, 'fries.jpg', 1),
(11, 'GUA-003', 'Alitas BBQ x6 Unidades', 'Alitas de pollo glaseadas en salsa barbacoa con bastones de apio', 4, 24.00, 12.00, 5, 10, 'fries.jpg', 1), -- Stock bajo (3)
(12, 'GUA-004', 'Tequeños de Queso x8', 'Crujientes tequeños rellenos de queso andino con guacamole tradicional', 4, 18.00, 7.50, 4, 8, 'fries.jpg', 1), -- Stock bajo (4)
(13, 'BEB-001', 'Refresco Personal 500ml', 'Bebida gaseosa helada en botella personal de 500ml', 3, 5.00, 2.00, 80, 20, NULL, 1),
(14, 'BEB-002', 'Agua Mineral 500ml', 'Agua mineral purificada sin gas con tapa rosca', 3, 3.50, 1.20, 65, 15, NULL, 1),
(15, 'BEB-003', 'Jugo Natural de Naranja 1L', 'Jugo recién exprimido de naranjas huando 100% natural', 3, 12.00, 4.50, 3, 8, NULL, 1), -- Stock bajo (5)
(16, 'BEB-004', 'Chicha Morada Clásica 1L', 'Preparada con maíz morado, piña, manzana y canela', 3, 10.00, 3.50, 22, 8, NULL, 1),
(17, 'BEB-005', 'Cerveza Artesanal 330ml', 'Cerveza rubia tipo Golden Ale elaborada en Lima', 3, 14.00, 7.00, 6, 12, NULL, 1), -- Stock bajo (6)
(18, 'POS-001', 'Torta de Chocolate Porción', 'Bizcocho húmedo de cacao cusqueño con abundante fudge de olla', 5, 12.00, 5.00, 4, 8, NULL, 1), -- Stock bajo (7)
(19, 'POS-002', 'Café Americano 8oz', 'Café pasado de origen Villa Rica, Chanchamayo', 5, 6.50, 2.00, 40, 10, NULL, 1),
(20, 'POS-003', 'Pie de Limón Artesanal', 'Base de galleta crujiente, crema cítrica y merengue suizo flameado', 5, 11.00, 4.20, 15, 6, NULL, 1);

-- Clientes registrados
INSERT INTO `clientes` (`id`, `tipo_documento`, `numero_documento`, `nombre`, `telefono`, `email`, `direccion`, `referencia`, `notas`, `activo`) VALUES
(1, 'DNI', '45892134', 'Ana Torres', '981234567', 'ana.torres@gmail.com', 'Calle Los Fresnos 240, Dpto 302, San Isidro', 'Frente al parque Roosevelt', 'Cliente frecuente, prefiere salsa tártara', 1),
(2, 'DNI', '72109845', 'Luis García', '976543210', 'lgarcia_peru@hotmail.com', 'Av. Arequipa 1450, Lince', 'Cerca a la estación México', 'Paga usualmente con Yape', 1),
(3, 'DNI', '09876543', 'María López', '998877665', 'mlopez.lima@outlook.com', 'Jr. Huancavelica 512, Lima Centro', 'A media cuadra de Iglesia Las Nazarenas', 'Llamar antes de entregar', 1),
(4, 'DNI', '41235678', 'Carlos Ruiz', '955443322', 'cruiz_ingenieria@yahoo.com', 'Av. Pardo 890, Dpto 402, Miraflores', 'Edificio Mirasol, puerta de vidrio', 'No tocar timbre fuerte (bebé durmiendo)', 1),
(5, 'DNI', '74561230', 'Sofía Ramírez', '944332211', 'sofia.ramirez@gmail.com', 'Calle Esperanza 123, Surco', 'A la espalda del centro comercial Chacarilla', 'Siempre solicita boleta interna detallada', 1),
(6, 'DNI', '00000000', 'Consumidor Final', '999999999', 'ventas@elbuensabor.pe', 'Local Comercial - Mostrador', 'Atención directa en tienda', 'Cliente de paso en mostrador', 1);

-- Pedidos iniciales
-- Distribución para hoy y días anteriores que totalizan las métricas del Dashboard:
-- Total pedidos activos: 18 pedidos en el sistema
-- Hoy:
-- PED-00018: Ana Torres - S/ 48.00 (En preparación, Domicilio)
-- PED-00017: Luis García - S/ 32.50 (Entregado, Recojo)
-- PED-00016: María López - S/ 76.90 (Confirmado, Domicilio)
-- PED-00015: Carlos Ruiz - S/ 54.00 (En camino, Domicilio)
-- PED-00014: Sofía Ramírez - S/ 28.50 (Pendiente, Recojo)
-- PED-00013: Ana Torres - S/ 65.00 (Entregado, Domicilio)
-- PED-00012: Consumidor Final - S/ 38.00 (Entregado, Recojo)
-- PED-00011: Luis García - S/ 92.50 (Entregado, Domicilio)
-- PED-00010: María López - S/ 54.00 (En preparación, Domicilio)
-- PED-00009: Carlos Ruiz - S/ 65.00 (En preparación, Recojo)
-- PED-00008: Sofía Ramírez - S/ 45.00 (Listo, Recojo)
-- PED-00007: Consumidor Final - S/ 43.10 (Entregado, Recojo)
-- (Suma de entregados hoy = 32.50 + 65.00 + 38.00 + 92.50 + 43.10 = S/ 271.10 + resto hoy = S/ 642.50)

INSERT INTO `pedidos` (`id`, `numero_pedido`, `cliente_id`, `usuario_id`, `modalidad`, `estado`, `subtotal`, `descuento`, `costo_envio`, `total`, `notas`, `estado_pago`, `monto_pagado`, `saldo_pendiente`, `direccion_entrega`, `fecha_pedido`) VALUES
(1, 'PED-00001', 1, 1, 'domicilio', 'entregado', 85.00, 5.00, 5.00, 85.00, 'Pedido completado sin incidencias', 'pagado', 85.00, 0.00, 'Calle Los Fresnos 240, San Isidro', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2, 'PED-00002', 2, 2, 'recojo', 'entregado', 110.00, 0.00, 0.00, 110.00, 'Recogido por cliente', 'pagado', 110.00, 0.00, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 'PED-00003', 3, 1, 'domicilio', 'entregado', 95.00, 0.00, 5.00, 100.00, 'Entregado a tiempo', 'pagado', 100.00, 0.00, 'Jr. Huancavelica 512, Lima Centro', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 'PED-00004', 4, 2, 'domicilio', 'cancelado', 50.00, 0.00, 5.00, 55.00, 'Cliente canceló por viaje imprevisto', 'pendiente', 0.00, 55.00, 'Av. Pardo 890, Miraflores', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 'PED-00005', 5, 1, 'recojo', 'entregado', 75.00, 5.00, 0.00, 70.00, 'Atención rápida', 'pagado', 70.00, 0.00, NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 'PED-00006', 1, 1, 'domicilio', 'entregado', 120.00, 10.00, 5.00, 115.00, 'Combo familiar de hamburguesas', 'pagado', 115.00, 0.00, 'Calle Los Fresnos 240, San Isidro', DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Pedidos del día de hoy (Calibrados para coincidir con la métrica ejecutiva)
(7, 'PED-00007', 6, 2, 'recojo', 'entregado', 43.10, 0.00, 0.00, 43.10, 'Consumo en mostrador', 'pagado', 43.10, 0.00, NULL, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(8, 'PED-00008', 5, 1, 'recojo', 'listo', 45.00, 0.00, 0.00, 45.00, 'Listo en bandeja para entrega', 'pagado', 45.00, 0.00, NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(9, 'PED-00009', 4, 1, 'recojo', 'en_preparacion', 65.00, 0.00, 0.00, 65.00, 'En cocina, masa estirada', 'parcial', 30.00, 35.00, NULL, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(10, 'PED-00010', 3, 2, 'domicilio', 'en_preparacion', 54.00, 0.00, 5.00, 59.00, 'Cocinando alitas y hamburguesas', 'pagado', 59.00, 0.00, 'Jr. Huancavelica 512, Lima Centro', DATE_SUB(NOW(), INTERVAL 100 MINUTE)),
(11, 'PED-00011', 2, 1, 'domicilio', 'entregado', 87.50, 0.00, 5.00, 92.50, 'Entregado conforme', 'pagado', 92.50, 0.00, 'Av. Arequipa 1450, Lince', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
(12, 'PED-00012', 6, 2, 'recojo', 'pendiente', 38.00, 0.00, 0.00, 38.00, 'Pizza mediana para llevar', 'pendiente', 0.00, 38.00, NULL, DATE_SUB(NOW(), INTERVAL 80 MINUTE)),
(13, 'PED-00013', 1, 1, 'domicilio', 'confirmado', 60.00, 0.00, 5.00, 65.00, 'Confirmado por WhatsApp', 'pagado', 65.00, 0.00, 'Calle Los Fresnos 240, San Isidro', DATE_SUB(NOW(), INTERVAL 70 MINUTE)),

-- 5 pedidos más recientes (visibles en el panel principal)
(14, 'PED-00014', 5, 2, 'recojo', 'pendiente', 28.50, 0.00, 0.00, 28.50, 'Sin hielo las bebidas por favor', 'pendiente', 0.00, 28.50, NULL, DATE_SUB(NOW(), INTERVAL 50 MINUTE)),
(15, 'PED-00015', 4, 1, 'domicilio', 'en_camino', 49.00, 0.00, 5.00, 54.00, 'Motorizado Juan en ruta', 'pagado', 54.00, 0.00, 'Av. Pardo 890, Dpto 402, Miraflores', DATE_SUB(NOW(), INTERVAL 35 MINUTE)),
(16, 'PED-00016', 3, 1, 'domicilio', 'confirmado', 71.90, 0.00, 5.00, 76.90, 'Enviar cubiertos descartables', 'pagado', 76.90, 0.00, 'Jr. Huancavelica 512, Lima Centro', DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(17, 'PED-00017', 2, 2, 'recojo', 'entregado', 32.50, 0.00, 0.00, 32.50, 'Cliente recogió en caja 1', 'pagado', 32.50, 0.00, NULL, DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(18, 'PED-00018', 1, 1, 'domicilio', 'en_preparacion', 43.00, 0.00, 5.00, 48.00, 'Salsas extras de ají pollero', 'pagado', 48.00, 0.00, 'Calle Los Fresnos 240, San Isidro', DATE_SUB(NOW(), INTERVAL 5 MINUTE));

-- Detalle de los pedidos
INSERT INTO `detalle_pedidos` (`pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `notas`) VALUES
-- PED-00018 (Total prod: 43.00 + 5 envio = 48.00)
(18, 1, 2, 16.50, 33.00, 'Término medio de la carne'),
(18, 13, 2, 5.00, 10.00, 'Inca Kola helada'),

-- PED-00017 (32.50)
(17, 2, 1, 22.00, 22.00, 'Bien caliente'),
(17, 9, 1, 9.00, 9.00, 'Con sal adicional'),
(17, 14, 1, 3.50, 3.50, 'Sin gas'),

-- PED-00016 (71.90 + 5 envio = 76.90)
(16, 5, 1, 42.00, 42.00, 'Masa delgada'),
(16, 11, 1, 24.00, 24.00, 'Picante medio'),
(16, 19, 1, 5.90, 5.90, 'Con stevia'),

-- PED-00015 (49.00 + 5 envio = 54.00)
(15, 6, 1, 32.00, 32.00, 'Corte en 8 porciones'),
(15, 9, 1, 9.00, 9.00, 'Papas crocantes'),
(15, 13, 2, 4.00, 8.00, 'Heladas'),

-- PED-00014 (28.50)
(14, 1, 1, 16.50, 16.50, 'Sin cebolla'),
(14, 18, 1, 12.00, 12.00, 'Para regalo');

-- Detalles adicionales históricos para calcular "Productos más vendidos":
-- 1: Hamburguesa Clásica (42 uds)
-- 2: Pizza Familiar (28 uds)
-- 3: Refresco 500ml (24 uds)
-- 4: Papas Fritas (18 uds)
-- 5: Agua 500ml (15 uds)
INSERT INTO `detalle_pedidos` (`pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 4, 16.50, 66.00),
(1, 13, 3, 5.00, 15.00),
(2, 5, 2, 42.00, 84.00),
(2, 9, 2, 9.00, 18.00),
(3, 1, 5, 16.50, 82.50),
(3, 14, 3, 3.50, 10.50),
(5, 5, 1, 42.00, 42.00),
(5, 1, 2, 16.50, 33.00),
(6, 1, 6, 16.50, 99.00),
(6, 13, 4, 5.00, 20.00),
(7, 1, 2, 16.50, 33.00),
(7, 14, 2, 3.50, 7.00),
(8, 5, 1, 42.00, 42.00),
(9, 1, 3, 16.50, 49.50),
(10, 1, 3, 16.50, 49.50),
(11, 5, 2, 42.00, 84.00),
(12, 6, 1, 32.00, 32.00),
(13, 1, 3, 16.50, 49.50);

-- Pagos registrados
INSERT INTO `pagos` (`pedido_id`, `usuario_id`, `metodo_pago`, `monto`, `referencia`, `fecha_pago`) VALUES
(1, 1, 'yape', 85.00, 'YAPE-90124', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2, 2, 'efectivo', 110.00, 'EF-CAJA-01', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 1, 'plin', 100.00, 'PLIN-34812', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(5, 1, 'tarjeta', 70.00, 'POS-VISA-5510', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 1, 'transferencia', 115.00, 'BCP-9812401', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 2, 'efectivo', 43.10, 'CAJA-DIR-07', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(8, 1, 'yape', 45.00, 'YAPE-44109', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(9, 1, 'efectivo', 30.00, 'ADELANTO-09', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(10, 2, 'plin', 59.00, 'PLIN-77819', DATE_SUB(NOW(), INTERVAL 100 MINUTE)),
(11, 1, 'yape', 92.50, 'YAPE-55120', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
(12, 2, 'efectivo', 38.00, 'CAJA-DIR-12', DATE_SUB(NOW(), INTERVAL 80 MINUTE)),
(13, 1, 'transferencia', 65.00, 'BBVA-1120', DATE_SUB(NOW(), INTERVAL 70 MINUTE)),
(15, 1, 'yape', 54.00, 'YAPE-66120', DATE_SUB(NOW(), INTERVAL 35 MINUTE)),
(16, 1, 'yape', 76.90, 'YAPE-88210', DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(17, 2, 'efectivo', 32.50, 'EF-CAJA-17', DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(18, 1, 'yape', 48.00, 'YAPE-99104', DATE_SUB(NOW(), INTERVAL 5 MINUTE));

-- Entregas asociadas a pedidos de modalidad domicilio
INSERT INTO `entregas` (`pedido_id`, `repartidor_nombre`, `repartidor_telefono`, `direccion`, `referencia`, `estado`, `fecha_salida`, `fecha_entrega`, `observaciones`) VALUES
(1, 'Mario Benítez', '991122334', 'Calle Los Fresnos 240, San Isidro', 'Frente al parque Roosevelt', 'entregado', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 'Entregado a tiempo'),
(3, 'Mario Benítez', '991122334', 'Jr. Huancavelica 512, Lima Centro', 'Iglesia Las Nazarenas', 'entregado', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 'Entregado sin novedades'),
(6, 'Juan Morales', '992233445', 'Calle Los Fresnos 240, San Isidro', 'Frente al parque Roosevelt', 'entregado', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Cliente dejó propina'),
(10, 'Juan Morales', '992233445', 'Jr. Huancavelica 512, Lima Centro', 'Iglesia Las Nazarenas', 'pendiente', NULL, NULL, 'Asignado para salir en cuanto cocina termine'),
(11, 'Mario Benítez', '991122334', 'Av. Arequipa 1450, Lince', 'Estación México', 'entregado', DATE_SUB(NOW(), INTERVAL 90 MINUTE), DATE_SUB(NOW(), INTERVAL 60 MINUTE), 'Entregado en portería'),
(13, 'Mario Benítez', '991122334', 'Calle Los Fresnos 240, San Isidro', 'Frente al parque', 'entregado', DATE_SUB(NOW(), INTERVAL 70 MINUTE), DATE_SUB(NOW(), INTERVAL 40 MINUTE), 'Entregado'),
(15, 'Juan Morales', '992233445', 'Av. Pardo 890 Dpto 402, Miraflores', 'Edificio Mirasol', 'en_camino', DATE_SUB(NOW(), INTERVAL 20 MINUTE), NULL, 'Repartidor en moto rumbo al destino'),
(16, 'Juan Morales', '992233445', 'Jr. Huancavelica 512, Lima Centro', 'Cerca a Las Nazarenas', 'pendiente', NULL, NULL, 'Programado para despacho'),
(18, 'Mario Benítez', '991122334', 'Calle Los Fresnos 240, San Isidro', 'Frente al parque Roosevelt', 'pendiente', NULL, NULL, 'En espera de preparación en cocina');

-- Historial de estados de pedidos
INSERT INTO `historial_estados_pedido` (`pedido_id`, `usuario_id`, `estado_anterior`, `estado_nuevo`, `motivo`) VALUES
(18, 1, NULL, 'pendiente', 'Creación del pedido'),
(18, 1, 'pendiente', 'confirmado', 'Pago recibido y verificado por Yape'),
(18, 1, 'confirmado', 'en_preparacion', 'Ingreso de comanda a cocina'),
(17, 2, NULL, 'pendiente', 'Registro en mostrador'),
(17, 2, 'pendiente', 'entregado', 'Cliente pagó y retiró su orden en mostrador'),
(16, 1, NULL, 'pendiente', 'Pedido registrado por teléfono'),
(16, 1, 'pendiente', 'confirmado', 'Pago confirmado por Yape'),
(15, 1, NULL, 'pendiente', 'Pedido web / WhatsApp'),
(15, 1, 'pendiente', 'confirmado', 'Pago verificado'),
(15, 1, 'confirmado', 'en_preparacion', 'Cocina preparó la orden'),
(15, 1, 'en_preparacion', 'listo', 'Empaquetado y listo para despacho'),
(15, 1, 'listo', 'en_camino', 'Entregado a repartidor Juan Morales en moto'),
(14, 2, NULL, 'pendiente', 'Pedido ingresado por teléfono, cliente recogerá en 30 min');
