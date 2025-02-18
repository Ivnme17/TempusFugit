CREATE DATABASE IF NOT EXISTS `tempus-fugit`;
USE `tempus-fugit`;

CREATE TABLE Roles (
    ID_rol INT PRIMARY KEY,
    tipo VARCHAR(20)
);

CREATE TABLE Usuarios (
    ID_Usuario CHAR(9) PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(100),
    contrasena VARCHAR(255),
    ID_rol INT,
    CONSTRAINT fk_usuarios_idRol FOREIGN KEY (ID_rol) REFERENCES Roles(ID_rol) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Clientes (
    ID_Cliente CHAR(9) PRIMARY KEY,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    IBAN VARCHAR(34),
    CONSTRAINT fk_clientes_idUsuario FOREIGN KEY (ID_Cliente) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Empleados (
    ID_Empleado CHAR(9) PRIMARY KEY,
    DNI VARCHAR(20),
    NSS VARCHAR(20),
    CONSTRAINT fk_empleados_idUsuario FOREIGN KEY (ID_Empleado) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Relojes (
    ID_reloj INT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    disponibilidad BOOLEAN DEFAULT TRUE,
    ID_Usuario INT,
    CONSTRAINT fk_relojes_idUsuario FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Servicios (
    ID_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('mantenimiento', 'reparación', 'cambio de batería'),
    precio_base DECIMAL(10, 2)
);

CREATE TABLE Ordenes_Servicio (
    ID_Usuario CHAR(9),
    ID_servicio INT,
    fecha_orden DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    PRIMARY KEY(ID_Usuario, ID_servicio, fecha_orden),
    CONSTRAINT fk_ordenes_idUsuarioCli FOREIGN KEY (ID_Usuario) REFERENCES Clientes(ID_Usuario) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT fk_ordenes_idServicio FOREIGN KEY (ID_servicio) REFERENCES Servicios(ID_servicio) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Gestion (
    ID_Usuario CHAR(9),
    ID_reloj INT,
    PRIMARY KEY(ID_Usuario, ID_reloj),
    CONSTRAINT fk_gestion_idClientes FOREIGN KEY (ID_Usuario) REFERENCES Clientes(ID_Cliente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idEmpleados FOREIGN KEY (ID_Usuario) REFERENCES Empleados(ID_Empleado) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idReloj FOREIGN KEY (ID_reloj) REFERENCES Relojes(ID_reloj) ON DELETE SET NULL ON UPDATE CASCADE
);