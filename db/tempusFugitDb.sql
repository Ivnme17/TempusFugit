CREATE DATABASE IF NOT EXISTS `tempus_fugit`;
USE `tempus_fugit`;
/*ELIMINAR EMPLEADO Y CLIENTE, SACAR MARCA Y MODELO DE LA TABLA RELOJES Y HACERLA TABLA, GESTIÓN SE ELIMINA,
DNI,NSS,TELEFONO,CORREO,DIRECCIÓN,IBAN SE VAN A USUARIOS, ELIMINAR TODOS LOS MANTENIMIENTOS, CAMBIAR DISPONIBILIDAD POR STOCK

--MAPA MENTAL-----------------------------------------------------------------------------------------------------------------

USUARIO TIENE ROL

REALIZA

PEDIDO

CON

RELOJES   PERTENECEN MARCA/MODELO

*/
CREATE TABLE roles (
    id_rol INT PRIMARY KEY,
    tipo VARCHAR(20)
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,  
    login VARCHAR(50) UNIQUE NOT NULL,          
    clave VARCHAR(255) NOT NULL,                
    id_rol INT,                                 
    nombre VARCHAR(50),                         
    apellidos VARCHAR(100),                     
    CONSTRAINT fk_usuarios_idrol FOREIGN KEY (id_rol) 
        REFERENCES roles(id_rol) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNIQUE NOT NULL,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    iban VARCHAR(34),
    CONSTRAINT fk_clientes_idusuario FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNIQUE NOT NULL,
    dni VARCHAR(20),
    nss VARCHAR(20),
    CONSTRAINT fk_empleados_idusuario FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE relojes (
    id_reloj INT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    tipo ENUM('digital', 'analógico'),
    disponibilidad BOOLEAN DEFAULT TRUE,
    id_usuario INT,
    url_imagen VARCHAR(255),
    CONSTRAINT fk_relojes_idusuario FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_servicio ENUM('mantenimiento', 'reparación', 'cambio de batería') NOT NULL,
    descripcion VARCHAR(255),
    precio_base DECIMAL(10, 2) NOT NULL,
    duracion_estimada INT,
    requiere_repuestos BOOLEAN DEFAULT FALSE
);

CREATE TABLE ordenes_servicio (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_servicio INT,
    fecha_orden DATETIME,
    estado ENUM('pendiente', 'en progreso', 'completado') DEFAULT 'pendiente',
    costo_total DECIMAL(10, 2),
    CONSTRAINT fk_ordenes_idcliente FOREIGN KEY (id_cliente) 
        REFERENCES clientes(id_cliente) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_ordenes_idservicio FOREIGN KEY (id_servicio) 
        REFERENCES servicios(id_servicio) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE gestion (
    id_cliente INT,
    id_empleado INT,
    id_reloj INT,
    PRIMARY KEY(id_cliente, id_empleado, id_reloj),
    CONSTRAINT fk_gestion_idcliente FOREIGN KEY (id_cliente) 
        REFERENCES clientes(id_cliente) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idempleado FOREIGN KEY (id_empleado) 
        REFERENCES empleados(id_empleado) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_gestion_idreloj FOREIGN KEY (id_reloj) 
        REFERENCES relojes(id_reloj) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

INSERT INTO roles (id_rol, tipo) VALUES
(1, 'Administrador'),
(2, 'Empleado'),
(3, 'Cliente');

INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos) VALUES
('juan.perez', 'D404559F602EAB6FD602AC7680DACBFAADD13630335E951F097AF3900E9DE176B6DB28512F2E000B9D04FBA5133E8B1C6E8DF59DB3A8AB9D60BE4B97CC9E81DB', 1, 'Juan', 'Pérez García'),
('ana.lopez', 'B109F3BBBC244EB82441917ED06D618B9008DD09B3BEFD1B5E07394C706A8BB980B1D7785E5976EC049B46DF5F1326AF5A2EA6D103FD07C95385FFAB0CACBC86', 2, 'Ana', 'López Martínez'),
('carlos.sanchez', '7469EB3DC5848B1DADD0F638A95CF4E4F0D6246717D5DC92E77B80F5199182B0F2CC1BB86C9187666B90ACA27372CDB03D22689A9343C5A96993BB1782F7A67D', 3, 'Carlos', 'Sánchez Fernández'),
('maria.gomez', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 2, 'María', 'Gómez Ruiz'),
('luis.martin', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 3, 'Luis', 'Martín Díaz'),
('elena.torres', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 3, 'Elena', 'Torres Vázquez');

INSERT INTO clientes (id_usuario, telefono, correo, direccion, iban) VALUES
((SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez'), '600123456', 'carlos.sanchez@example.com', 'Calle Falsa 123, Madrid', 'ES9121000418450200051332'),
((SELECT id_usuario FROM usuarios WHERE login = 'luis.martin'), '611234567', 'luis.martin@example.com', 'Avenida Real 45, Barcelona', 'ES7621000418450200056789'),
((SELECT id_usuario FROM usuarios WHERE login = 'elena.torres'), '622345678', 'elena.torres@example.com', 'Plaza Mayor 67, Valencia', 'ES4521000418450200059876');

INSERT INTO empleados (id_usuario, dni, nss) VALUES
((SELECT id_usuario FROM usuarios WHERE login = 'ana.lopez'), '12345678A', '281234567890'),
((SELECT id_usuario FROM usuarios WHERE login = 'maria.gomez'), '87654321B', '289876543210');

INSERT INTO relojes (id_reloj, marca, modelo, precio, tipo, disponibilidad, id_usuario, url_imagen) VALUES
(1, 'Rolex', 'Submariner', 8500.00, 'analógico', TRUE, (SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez'), 'https://www.rabat.net/media/catalog/product/r/o/rolex-submariner-m126610ln-0001.png'),
(2, 'Casio', 'G-Shock', 150.00, 'digital', TRUE, NULL, 'https://www.baroli.es/wp-content/uploads/2015/12/GA-120BB-1AER.jpg'),
(3, 'Rolex', 'Datejust', 7200.00, 'analógico', FALSE, (SELECT id_usuario FROM usuarios WHERE login = 'luis.martin'), 'https://media.rolex.com/image/upload/q_auto:eco/f_auto/t_v7-majesty/c_limit,w_3840/v1/catalogue/2024/upright-c/m126200-0005'),
(4, 'Lotus', 'Multifunction', 220.00, 'analógico', TRUE, NULL, 'https://static6.festinagroup.com/product/lotus/watches/detail/big/l18812_3.webp'),
(5, 'Casio', 'Edifice', 180.00, 'digital', TRUE, (SELECT id_usuario FROM usuarios WHERE login = 'elena.torres'), 'https://www.timeshop24.es/media/catalog/product/cache/1bc0b3bc127023c7949db1e873983161/e/f/ef-539d-1avef.webp');

INSERT INTO servicios (tipo_servicio, descripcion, precio_base, duracion_estimada, requiere_repuestos) VALUES
('mantenimiento', 'Revisión general y limpieza del reloj', 50.00, 60, FALSE),
('reparación', 'Reparación de mecanismo básico', 100.00, 120, TRUE),
('cambio de batería', 'Reemplazo de batería en relojes digitales', 20.00, 30, TRUE),
('reparación', 'Reparación de daños superficiales', 30.00, 90, FALSE),
('reparación', 'Reparación de correa o brazalete', 15.00, 45, TRUE);

INSERT INTO ordenes_servicio (id_cliente, id_servicio, fecha_orden, estado, costo_total) VALUES
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez')), 1, '2023-10-01 10:00:00', 'pendiente', 50.00),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez')), 2, '2023-10-02 11:00:00', 'en progreso', 100.00),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'luis.martin')), 3, '2023-10-03 12:00:00', 'completado', 20.00),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'elena.torres')), 4, '2023-10-04 13:00:00', 'pendiente', 30.00),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'luis.martin')), 5, '2023-10-05 14:00:00', 'en progreso', 15.00);

INSERT INTO gestion (id_cliente, id_empleado, id_reloj) VALUES
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT id_empleado FROM empleados WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'ana.lopez')), 1),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'luis.martin')), 
 (SELECT id_empleado FROM empleados WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'maria.gomez')), 3),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'elena.torres')), 
 (SELECT id_empleado FROM empleados WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'ana.lopez')), 5),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'carlos.sanchez')), 
 (SELECT id_empleado FROM empleados WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'maria.gomez')), 2),
((SELECT id_cliente FROM clientes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'luis.martin')), 
 (SELECT id_empleado FROM empleados WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE login = 'ana.lopez')), 4);
