CREATE DATABASE IF NOT EXISTS tempus_fugit;
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

CREATE TABLE relojes (
    id_reloj INT AUTO_INCREMENT PRIMARY KEY,
    id_marca_modelo INT NOT NULL,
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    stock INT DEFAULT 0,
    url_imagen VARCHAR(255),
    CONSTRAINT fk_relojes_marca_modelo FOREIGN KEY (id_marca_modelo)
        REFERENCES marca_modelo(id_marca_modelo)
        ON UPDATE CASCADE
);

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_reloj INT NOT NULL,
    fecha_pedido DATETIME NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    precio_total DECIMAL(10, 2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    metodo_pago ENUM('tarjeta', 'BIZUM'),
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_pedidos_reloj FOREIGN KEY (id_reloj)
        REFERENCES relojes(id_reloj)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE detalles_pedido (
    id_detalle_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_usuario INT NOT NULL,
    precio_base DECIMAL(10, 2) NOT NULL,
    descuento_porcentaje DECIMAL(5, 2) DEFAULT 0.00,
    impuesto_porcentaje DECIMAL(5, 2) DEFAULT 21.00,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    precio_final DECIMAL(10, 2) GENERATED ALWAYS AS (precio_base * (1 - descuento_porcentaje/100) * (1 + impuesto_porcentaje/100)) STORED,
    notas VARCHAR(255),
    CONSTRAINT fk_detalles_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detalles_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO roles (tipo) VALUES
('Administrador'),
('Empleado'),
('Cliente');

INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos, dni, nss, telefono, correo, direccion, iban) VALUES
('juan.perez', 'D404559F602EAB6FD602AC7680DACBFAADD13630335E951F097AF3900E9DE176B6DB28512F2E000B9D04FBA5133E8B1C6E8DF59DB3A8AB9D60BE4B97CC9E81DB', 1, 'Juan', 'Pérez García', NULL, NULL, NULL, NULL, NULL, NULL),
('ana.lopez', '31655FC0C371CF573BDFDF9EBED4E6BF0E0B44DACF8087D87D00D559F92383FF6CC32F2D8EA70E6EA1CEA5DA376186B4DBC88F651095518E3E6DE0AE0065A6FC', 2, 'Ana', 'López Martínez', '23456789B', '281234567890', '600111222', 'ana.lopez@example.com', 'Calle Mayor 10, Madrid', NULL),
('carlos.sanchez', '7469EB3DC5848B1DADD0F638A95CF4E4F0D6246717D5DC92E77B80F5199182B0F2CC1BB86C9187666B90ACA27372CDB03D22689A9343C5A96993BB1782F7A67D', 3, 'Carlos', 'Sánchez Fernández', NULL, NULL, '600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332');

INSERT INTO marca_modelo (marca, modelo) VALUES
('Rolex', 'Submariner'),
('Rolex', 'Datejust'),
('Casio', 'G-Shock'),
('Casio', 'Edifice');

INSERT INTO relojes (id_marca_modelo, precio, tipo, stock, url_imagen) VALUES
(1, 8500.00, 'analógico', 5, 'https://www.rabat.net/media/catalog/product/r/o/rolex-submariner-m126610ln-0001.png'),
(2, 7200.00, 'analógico', 3, 'https://media.rolex.com/image/upload/q_auto:eco/f_auto/t_v7-majesty/c_limit,w_3840/v1/catalogue/2024/upright-c/m126200-0005'),
(3, 150.00, 'digital', 10, 'https://www.baroli.es/wp-content/uploads/2015/12/GA-120BB-1AER.jpg'),
(4, 180.00, 'digital', 12, 'https://www.timeshop24.es/media/catalog/product/cache/1bc0b3bc127023c7949db1e873983161/e/f/ef-539d-1avef.webp');

INSERT INTO pedidos (id_usuario, id_reloj, fecha_pedido, cantidad, precio_unitario, metodo_pago) VALUES
(3, 1, '2024-04-01 10:00:00', 1, 8500.00, 'BIZUM'),
(3, 3, '2024-04-02 15:30:00', 1, 150.00, 'BIZUM');

INSERT INTO detalles_pedido (id_pedido, id_usuario, precio_base, descuento_porcentaje, impuesto_porcentaje, notas) VALUES
(1, 3, 7024.79, 0.00, 21.00, 'Precio premium de Rolex Submariner'),
(2, 3, 123.97, 0.00, 21.00, 'Precio económico para G-Shock');
