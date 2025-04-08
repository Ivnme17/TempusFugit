CREATE DATABASE IF NOT EXISTS `tempus_fugit`;
USE `tempus_fugit`;

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(20) NOT NULL
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    id_rol INT,
    nombre VARCHAR(50),
    apellidos VARCHAR(100),
    dni VARCHAR(20) DEFAULT NULL,
    nss VARCHAR(20) DEFAULT NULL,
    telefono VARCHAR(15) DEFAULT NULL,
    correo VARCHAR(100) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    iban VARCHAR(34) DEFAULT NULL,
    CONSTRAINT fk_usuarios_idrol FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE marca_modelo (
    id_marca_modelo INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    CONSTRAINT uq_marca_modelo UNIQUE (marca, modelo)
);

CREATE TABLE detalles_precio (
    id_detalle_precio INT AUTO_INCREMENT PRIMARY KEY,
    precio_base DECIMAL(10, 2) NOT NULL,
    descuento_porcentaje DECIMAL(5, 2) DEFAULT 0.00,
    impuesto_porcentaje DECIMAL(5, 2) DEFAULT 21.00,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    precio_final DECIMAL(10, 2) GENERATED ALWAYS AS (precio_base * (1 - descuento_porcentaje/100) * (1 + impuesto_porcentaje/100)) STORED,
    notas VARCHAR(255)
);

CREATE TABLE relojes (
    id_reloj INT AUTO_INCREMENT PRIMARY KEY,
    id_marca_modelo INT NOT NULL,
    precio DECIMAL(10, 2),
    id_detalle_precio INT,
    tipo ENUM('digital', 'analógico'),
    stock INT DEFAULT 0,
    url_imagen VARCHAR(255),
    CONSTRAINT fk_relojes_marca_modelo FOREIGN KEY (id_marca_modelo)
        REFERENCES marca_modelo(id_marca_modelo)
        ON UPDATE CASCADE,
    CONSTRAINT fk_relojes_detalle_precio FOREIGN KEY (id_detalle_precio)
        REFERENCES detalles_precio(id_detalle_precio)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_reloj INT,
    fecha_pedido DATETIME,
    fecha_entrega_estimada DATE,
    fecha_entrega_real DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    precio_total DECIMAL(10, 2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    direccion_entrega VARCHAR(255),
    metodo_pago ENUM('tarjeta', 'transferencia', 'contra reembolso', 'PayPal'),
    codigo_seguimiento VARCHAR(50),
    notas_pedido TEXT,
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_pedidos_reloj FOREIGN KEY (id_reloj)
        REFERENCES relojes(id_reloj)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO roles (tipo) VALUES
('Administrador'),
('Empleado'),
('Cliente');

INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos, dni, nss, telefono, correo, direccion, iban) VALUES
('juan.perez', 'D404559F602EAB6FD602AC7680DACBFAADD13630335E951F097AF3900E9DE176B6DB28512F2E000B9D04FBA5133E8B1C6E8DF59DB3A8AB9D60BE4B97CC9E81DB', 1, 'Juan', 'Pérez García', NULL, NULL, NULL, NULL, NULL, NULL),
('ana.lopez', 'B109F3BBBC244EB82441917ED06D618B9008DD09B3BEFD1B5E07394C706A8BB980B1D7785E5976EC049B46DF5F1326AF5A2EA6D103FD07C95385FFAB0CACBC86', 2, 'Ana', 'López Martínez', '23456789B', '281234567890', '600111222', 'ana.lopez@example.com', 'Calle Mayor 10, Madrid', NULL),
('carlos.sanchez', '7469EB3DC5848B1DADD0F638A95CF4E4F0D6246717D5DC92E77B80F5199182B0F2CC1BB86C9187666B90ACA27372CDB03D22689A9343C5A96993BB1782F7A67D', 3, 'Carlos', 'Sánchez Fernández', NULL, NULL, '600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332');

INSERT INTO marca_modelo (marca, modelo) VALUES
('Rolex', 'Submariner'),
('Rolex', 'Datejust'),
('Casio', 'G-Shock'),
('Casio', 'Edifice'),
('Lotus', 'Multifunction');

INSERT INTO detalles_precio (precio_base, descuento_porcentaje, impuesto_porcentaje, notas) VALUES
(7024.79, 0.00, 21.00, 'Precio premium de Rolex Submariner'),
(5950.41, 0.00, 21.00, 'Precio estándar de Rolex Datejust'),
(123.97, 0.00, 21.00, 'Precio económico para G-Shock'),
(148.76, 0.00, 21.00, 'Precio competitivo para Edifice'),
(181.82, 0.00, 21.00, 'Precio medio para Lotus Multifunction');

INSERT INTO relojes (id_marca_modelo, precio, id_detalle_precio, tipo, stock, url_imagen) VALUES
(1, 8500.00, 1, 'analógico', 5, 'https://www.rabat.net/media/catalog/product/r/o/rolex-submariner-m126610ln-0001.png'),
(2, 7200.00, 2, 'analógico', 3, 'https://media.rolex.com/image/upload/q_auto:eco/f_auto/t_v7-majesty/c_limit,w_3840/v1/catalogue/2024/upright-c/m126200-0005'),
(3, 150.00, 3, 'digital', 10, 'https://www.baroli.es/wp-content/uploads/2015/12/GA-120BB-1AER.jpg'),
(4, 180.00, 4, 'digital', 12, 'https://www.timeshop24.es/media/catalog/product/cache/1bc0b3bc127023c7949db1e873983161/e/f/ef-539d-1avef.webp'),
(5, 220.00, 5, 'analógico', 8, 'https://static6.festinagroup.com/product/lotus/watches/detail/big/l18812_3.webp');

INSERT INTO pedidos (id_usuario, id_reloj, fecha_pedido, fecha_entrega_estimada, estado, cantidad, precio_unitario, direccion_entrega, metodo_pago) VALUES
(3, 1, '2024-04-01 10:00:00', '2024-04-08', 'pendiente', 1, 8500.00, 'Calle Falsa 123, Madrid', 'tarjeta'),
(3, 3, '2024-04-02 15:30:00', '2024-04-09', 'en progreso', 1, 150.00, 'Calle Falsa 123, Madrid', 'transferencia');