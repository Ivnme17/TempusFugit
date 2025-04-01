CREATE DATABASE IF NOT EXISTS `tempus-fugit`;
USE `tempus-fugit`;

CREATE TABLE Roles (
    ID_rol INT PRIMARY KEY,
    tipo VARCHAR(20)
);

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,  
    login VARCHAR(50) UNIQUE NOT NULL,          
    clave VARCHAR(255) NOT NULL,                
    id_rol INT,                                 
    nombre VARCHAR(50),                         
    apellidos VARCHAR(100),                     
    CONSTRAINT fk_usuarios_idRol FOREIGN KEY (id_rol) 
        REFERENCES Roles(ID_rol) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

CREATE TABLE Clientes (
    ID_Cliente INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    IBAN VARCHAR(34),
    CONSTRAINT fk_clientes_idUsuario FOREIGN KEY (ID_Usuario) 
        REFERENCES Usuarios(ID_Usuario) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE Empleados (
    ID_Empleado INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT UNIQUE NOT NULL,
    DNI VARCHAR(20),
    NSS VARCHAR(20),
    CONSTRAINT fk_empleados_idUsuario FOREIGN KEY (ID_Usuario) 
        REFERENCES Usuarios(ID_Usuario) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE Relojes (
    ID_reloj INT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    disponibilidad BOOLEAN DEFAULT TRUE,
    ID_Usuario INT,
    url_imagen VARCHAR(255),
    CONSTRAINT fk_relojes_idUsuario FOREIGN KEY (ID_Usuario) 
        REFERENCES Usuarios(ID_Usuario) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);


CREATE TABLE Servicios (
    ID_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('mantenimiento', 'reparación', 'cambio de batería') NOT NULL,
    descripcion VARCHAR(255),
    precio_base DECIMAL(10, 2) NOT NULL,
    duracion_estimada INT,
    requiere_repuestos BOOLEAN DEFAULT FALSE
);


CREATE TABLE Ordenes_Servicio (
    ID_Orden INT AUTO_INCREMENT PRIMARY KEY,
    ID_Cliente INT,
    ID_servicio INT,
    fecha_orden DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    CONSTRAINT fk_ordenes_idCliente FOREIGN KEY (ID_Cliente) 
        REFERENCES Clientes(ID_Cliente) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_ordenes_idServicio FOREIGN KEY (ID_servicio) 
        REFERENCES Servicios(ID_servicio) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE Gestion (
    ID_Cliente INT,
    ID_Empleado INT,
    ID_reloj INT,
    PRIMARY KEY(ID_Cliente, ID_Empleado, ID_reloj),
    CONSTRAINT fk_gestion_idCliente FOREIGN KEY (ID_Cliente) 
        REFERENCES Clientes(ID_Cliente) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idEmpleado FOREIGN KEY (ID_Empleado) 
        REFERENCES Empleados(ID_Empleado) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idReloj FOREIGN KEY (ID_reloj) 
        REFERENCES Relojes(ID_reloj) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

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

INSERT INTO Clientes (ID_Usuario, telefono, correo, direccion, IBAN) VALUES
((SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez'), '600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332'),
((SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin'), '611234567', 'luis.martin@example.com', 'Avenida Real 45, Barcelona', 'ES7621000418450200056789'),
((SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres'), '622345678', 'elena.torres@example.com', 'Plaza Mayor 67, Valencia', 'ES4521000418450200059876');

INSERT INTO Empleados (ID_Usuario, DNI, NSS) VALUES
((SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez'), '12345678A', '281234567890'),
((SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez'), '87654321B', '289876543210');

INSERT INTO Relojes (ID_reloj, marca, modelo, precio, tipo, disponibilidad, ID_Usuario, url_imagen) VALUES
(1, 'Rolex', 'Submariner', 8500.00, 'analógico', TRUE, (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez'), 'https://www.rabat.net/media/catalog/product/r/o/rolex-submariner-m126610ln-0001.png'),
(2, 'Casio', 'G-Shock', 150.00, 'digital', TRUE, NULL, 'https://www.baroli.es/wp-content/uploads/2015/12/GA-120BB-1AER.jpg'),
(3, 'Rolex', 'Datejust', 7200.00, 'analógico', FALSE, (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin'), 'https://media.rolex.com/image/upload/q_auto:eco/f_auto/t_v7-majesty/c_limit,w_3840/v1/catalogue/2024/upright-c/m126200-0005'),
(4, 'Lotus', 'Multifunction', 220.00, 'analógico', TRUE, NULL, 'https://static6.festinagroup.com/product/lotus/watches/detail/big/l18812_3.webp'),
(5, 'Casio', 'Edifice', 180.00, 'digital', TRUE, (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres'), 'https://www.timeshop24.es/media/catalog/product/cache/1bc0b3bc127023c7949db1e873983161/e/f/ef-539d-1avef.webp');

INSERT INTO Servicios (tipo_servicio, descripcion, precio_base, duracion_estimada, requiere_repuestos) VALUES
('mantenimiento', 'Revisión general y limpieza del reloj', 50.00, 60, FALSE),
('reparación', 'Reparación de mecanismo básico', 100.00, 120, TRUE),
('cambio de batería', 'Reemplazo de batería en relojes digitales', 20.00, 30, TRUE),
('reparación', 'Reparación de daños superficiales', 30.00, 90, FALSE),
('reparación', 'Reparación de correa o brazalete', 15.00, 45, TRUE);
INSERT INTO Ordenes_Servicio (ID_Cliente, ID_servicio, fecha_orden, estado, costo_total) VALUES
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 1, '2023-10-01 10:00:00', 'pendiente', 50.00),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 2, '2023-10-02 11:00:00', 'en progreso', 100.00),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 3, '2023-10-03 12:00:00', 'completado', 20.00),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres')), 4, '2023-10-04 13:00:00', 'pendiente', 30.00),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 5, '2023-10-05 14:00:00', 'en progreso', 15.00);

INSERT INTO Gestion (ID_Cliente, ID_Empleado, ID_reloj) VALUES
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT ID_Empleado FROM Empleados WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 1),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 
 (SELECT ID_Empleado FROM Empleados WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez')), 3),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'elena.torres')), 
 (SELECT ID_Empleado FROM Empleados WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 5),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT ID_Empleado FROM Empleados WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'maria.gomez')), 2),
((SELECT ID_Cliente FROM Clientes WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'luis.martin')), 
 (SELECT ID_Empleado FROM Empleados WHERE ID_Usuario = (SELECT ID_Usuario FROM Usuarios WHERE login = 'ana.lopez')), 4);