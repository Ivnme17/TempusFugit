CREATE DATABASE IF NOT EXISTS `tempus-fugit`;/*Creamos la base de datos*/

USE `tempus-fugit`;/*Usamos la base de datos*/

/*CREAMOS LAS TABLAS QUE VAN A FORMAR PARTE DE LA BASE DE DATOS*/

CREATE TABLE Roles (
    ID_rol INT(11) PRIMARY KEY,
    tipo VARCHAR(20)   
)DEFAULT CHARSET=utf8mb4 COLLATE=utf8_general_ci;



CREATE TABLE Clientes (
    ID_cliente INT PRIMARY KEY,
    nombre VARCHAR(50),
    apellido VARCHAR(100),
    correo VARCHAR(100),
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    ID_rol INT(11) ,
    CONSTRAINT fk_clientes_idRol FOREIGN KEY (ID_rol) REFERENCES Roles(ID_rol) ON DELETE SET NULL ON UPDATE CASCADE
);


CREATE TABLE Relojes (
    ID_reloj INT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    disponibilidad BOOLEAN DEFAULT TRUE
);


CREATE TABLE Ventas (
    ID_venta INT AUTO_INCREMENT PRIMARY KEY,
    ID_cliente INT,
    ID_reloj INT,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    cantidad INT,
    total_venta DECIMAL(10, 2),
    CONSTRAINT fk_ventas_idCliente FOREIGN KEY (ID_cliente) REFERENCES Clientes(ID_cliente) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_ventas_idReloj FOREIGN KEY (ID_reloj) REFERENCES Relojes(ID_reloj) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Servicios (
    ID_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('mantenimiento', 'reparación', 'cambio de batería'),
    precio_base DECIMAL(10, 2)
);

CREATE TABLE Ordenes_Servicio (
    ID_orden INT AUTO_INCREMENT PRIMARY KEY,
    ID_cliente INT,
    ID_servicio INT,
    fecha_orden DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    CONSTRAINT fk_ordenes_idCliente FOREIGN KEY (ID_cliente) REFERENCES Clientes(ID_cliente) ON DELETE SET NULL  ON UPDATE CASCADE ,
    CONSTRAINT fk_ordenes_idServicio FOREIGN KEY (ID_servicio) REFERENCES Servicios(ID_servicio) ON DELETE SET NULL ON UPDATE CASCADE
);