CREATE DATABASE IF NOT EXISTS `tempus-fugit`;
USE `tempus-fugit`;

CREATE TABLE Roles (
    ID_rol INT PRIMARY KEY,
    tipo VARCHAR(20)
);

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,  -- Added auto-incrementing ID
    login VARCHAR(50) UNIQUE NOT NULL,          -- Kept login as unique
    clave VARCHAR(255) NOT NULL,                -- Password
    id_rol INT,                                 -- Role reference
    nombre VARCHAR(50),                         -- First name
    apellidos VARCHAR(100),                     -- Last name
    CONSTRAINT fk_usuarios_idRol FOREIGN KEY (id_rol) REFERENCES Roles(ID_rol) ON DELETE SET NULL ON UPDATE CASCADE
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
    ID_Usuario CHAR(9),
    CONSTRAINT fk_relojes_idUsuario FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Servicios (
    ID_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('mantenimiento', 'reparación', 'cambio de batería'),
    precio_base DECIMAL(10, 2)
);

CREATE TABLE Ordenes_Servicio (
    ID_Cliente CHAR(9),
    ID_servicio INT,
    fecha_orden DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    PRIMARY KEY(ID_Cliente, ID_servicio, fecha_orden),
    CONSTRAINT fk_ordenes_idCliente FOREIGN KEY (ID_Cliente) REFERENCES Clientes(ID_Cliente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ordenes_idServicio FOREIGN KEY (ID_servicio) REFERENCES Servicios(ID_servicio) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Gestion (
    ID_Cliente CHAR(9),
    ID_Empleado CHAR(9),
    ID_reloj INT,
    PRIMARY KEY(ID_Cliente, ID_Empleado, ID_reloj),
    CONSTRAINT fk_gestion_idCliente FOREIGN KEY (ID_Cliente) REFERENCES Clientes(ID_Cliente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idEmpleado FOREIGN KEY (ID_Empleado) REFERENCES Empleados(ID_Empleado) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idReloj FOREIGN KEY (ID_reloj) REFERENCES Relojes(ID_reloj) ON DELETE CASCADE ON UPDATE CASCADE
);


/*REGISTROS PARA CADA TABLA*/

INSERT INTO Roles (ID_rol, tipo) VALUES
(1, 'Administrador'),
(2, 'Empleado'),
(3, 'Cliente');


INSERT INTO Usuarios (login, clave, id_rol, nombre, apellidos) VALUES
('juan.perez', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 1, 'Juan', 'Pérez García'),
('ana.lopez', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2, 'Ana', 'López Martínez'),
('carlos.sanchez', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 3, 'Carlos', 'Sánchez Fernández'),
('maria.gomez', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 2, 'María', 'Gómez Ruiz'),
('luis.martin', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 3, 'Luis', 'Martín Díaz'),
('elena.torres', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 3, 'Elena', 'Torres Vázquez');

INSERT INTO Clientes (ID_Cliente, telefono, correo, direccion, IBAN) VALUES
('U003', '600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332'),
('U005', '611234567', 'luis.martin@example.com', 'Avenida Real 45, Barcelona', 'ES7621000418450200056789'),
('U006', '622345678', 'elena.torres@example.com', 'Plaza Mayor 67, Valencia', 'ES4521000418450200059876');

INSERT INTO Empleados (ID_Empleado, DNI, NSS) VALUES
('U002', '12345678A', '281234567890'),
('U004', '87654321B', '289876543210');

INSERT INTO Relojes (ID_reloj, marca, modelo, precio, tipo, disponibilidad, ID_Usuario) VALUES
(1, 'Rolex', 'Submariner', 8500.00, 'analógico', TRUE, 'U003'),
(2, 'Casio', 'G-Shock', 150.00, 'digital', TRUE, NULL),
(3, 'Omega', 'Speedmaster', 5000.00, 'analógico', FALSE, 'U005'),
(4, 'Seiko', 'Presage', 1200.00, 'analógico', TRUE, NULL),
(5, 'Apple', 'Watch Series 8', 400.00, 'digital', TRUE, 'U006');

INSERT INTO Servicios (tipo_servicio, precio_base) VALUES
('mantenimiento', 50.00),
('reparación', 100.00),
('cambio de batería', 20.00),
('reparación', 30.00),
('reparación', 15.00);


INSERT INTO Ordenes_Servicio (ID_Cliente, ID_servicio, fecha_orden, estado, costo_total) VALUES
('U003', 1, '2023-10-01 10:00:00', 'pendiente', 50.00),
('U003', 2, '2023-10-02 11:00:00', 'en progreso', 100.00),
('U005', 3, '2023-10-03 12:00:00', 'completado', 20.00),
('U006', 4, '2023-10-04 13:00:00', 'pendiente', 30.00),
('U005', 5, '2023-10-05 14:00:00', 'en progreso', 15.00);



INSERT INTO Gestion (ID_Cliente, ID_Empleado, ID_reloj) VALUES
('U003', 'U002', 1),
('U005', 'U004', 3),
('U006', 'U002', 5),
('U003', 'U004', 2),
('U005', 'U002', 4);