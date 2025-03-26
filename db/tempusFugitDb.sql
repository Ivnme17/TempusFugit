-- Update Clientes table
CREATE TABLE Clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    IBAN VARCHAR(34),
    id_usuario INT,
    CONSTRAINT fk_clientes_idUsuario FOREIGN KEY (id_usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Update Empleados table
CREATE TABLE Empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    DNI VARCHAR(20),
    NSS VARCHAR(20),
    id_usuario INT,
    CONSTRAINT fk_empleados_idUsuario FOREIGN KEY (id_usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Update Relojes table (already uses INT for ID_Usuario, so just rename)
CREATE TABLE Relojes (
    ID_reloj INT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    disponibilidad BOOLEAN DEFAULT TRUE,
    id_usuario INT,
    CONSTRAINT fk_relojes_idUsuario FOREIGN KEY (id_usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Update Ordenes_Servicio table
CREATE TABLE Ordenes_Servicio (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    ID_servicio INT,
    fecha_orden DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    CONSTRAINT fk_ordenes_idCliente FOREIGN KEY (id_cliente) REFERENCES Clientes(id_cliente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ordenes_idServicio FOREIGN KEY (ID_servicio) REFERENCES Servicios(ID_servicio) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Update Gestion table
CREATE TABLE Gestion (
    id_gestion INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_empleado INT,
    ID_reloj INT,
    CONSTRAINT fk_gestion_idCliente FOREIGN KEY (id_cliente) REFERENCES Clientes(id_cliente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idEmpleado FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idReloj FOREIGN KEY (ID_reloj) REFERENCES Relojes(ID_reloj) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Usuarios already updated in previous message

-- Insert Clientes using the id_usuario from the Usuarios table
INSERT INTO Clientes (telefono, correo, direccion, IBAN, id_usuario) VALUES
('600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332', 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')),
('611234567', 'luis.martin@example.com', 'Avenida Real 45, Barcelona', 'ES7621000418450200056789', 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')),
('622345678', 'elena.torres@example.com', 'Plaza Mayor 67, Valencia', 'ES4521000418450200059876', 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres'));

-- Insert Empleados
INSERT INTO Empleados (DNI, NSS, id_usuario) VALUES
('12345678A', '281234567890', (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')),
('87654321B', '289876543210', (SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez'));

-- Relojes insert (keeping original structure)
INSERT INTO Relojes (ID_reloj, marca, modelo, precio, tipo, disponibilidad, id_usuario) VALUES
(1, 'Rolex', 'Submariner', 8500.00, 'analógico', TRUE, 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')),
(2, 'Casio', 'G-Shock', 150.00, 'digital', TRUE, NULL),
(3, 'Omega', 'Speedmaster', 5000.00, 'analógico', FALSE, 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')),
(4, 'Seiko', 'Presage', 1200.00, 'analógico', TRUE, NULL),
(5, 'Apple', 'Watch Series 8', 400.00, 'digital', TRUE, 
    (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres'));

-- Ordenes_Servicio insert
INSERT INTO Ordenes_Servicio (id_cliente, ID_servicio, fecha_orden, estado, costo_total) VALUES
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 1, '2023-10-01 10:00:00', 'pendiente', 50.00),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 2, '2023-10-02 11:00:00', 'en progreso', 100.00),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 3, '2023-10-03 12:00:00', 'completado', 20.00),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres')), 4, '2023-10-04 13:00:00', 'pendiente', 30.00),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 5, '2023-10-05 14:00:00', 'en progreso', 15.00);

-- Gestion insert
INSERT INTO Gestion (id_cliente, id_empleado, ID_reloj) VALUES
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT id_empleado FROM Empleados WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 1),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 
 (SELECT id_empleado FROM Empleados WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez')), 3),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres')), 
 (SELECT id_empleado FROM Empleados WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 5),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT id_empleado FROM Empleados WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez')), 2),
((SELECT id_cliente FROM Clientes WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 
 (SELECT id_empleado FROM Empleados WHERE id_usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 4);