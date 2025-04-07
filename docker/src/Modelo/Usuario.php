<?php
require_once './Servicio/Db.php';

class Usuario {
    private $login;
    private $clave;
    private $id_rol;
    private $nombre;
    private $apellidos;
    private $email;
    private $fecha_registro;
    private $activo;
    
    public function __construct() {
        $this->fecha_registro = date('Y-m-d H:i:s');
        $this->activo = true;
    }
    
    public function getApellidos() {
        return $this->apellidos;
    }
    
    public function setApellidos($apellidos): void {
        $this->apellidos = trim($apellidos);
    }

    public function getId_rol() {
        return $this->id_rol;
    }

    public function setId_rol($id_rol): void {
        $this->id_rol = $id_rol;
    }

    public function getNombre() {
        return $this->nombre;
    }
    public function setNombre($nombre): void {
        $this->nombre = trim($nombre);
    }
    public function getLogin() {
        return $this->login;
    }
    public function setLogin($login): void {
        $this->login = trim($login);
    }

    public function getClave() {
        return $this->clave;
    }

    public function setClave($clave): void {
        $this->clave = trim($clave);
    }
    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email): void {
        $this->email = trim($email);
    }
    public function getFecha_registro() {
        return $this->fecha_registro;
    }
    public function setFecha_registro($fecha_registro): void {
        $this->fecha_registro = $fecha_registro;
    }
    public function getActivo() {
        return $this->activo;
    }
    public function setActivo($activo): void {
        $this->activo = $activo;
    }

    public static function obtenerUsuarioPorId($id) {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        return $stmt->fetch();
    }

    public static function obtenerTodosUsuarios() {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('SELECT * FROM usuarios');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        return $stmt->fetchAll();
    }

    public function añadirUsuario() {
        $conexion = Db::getConexion();
        $exito = false;
        
        try {
            $conexion->beginTransaction();
            
            $stmt = $conexion->prepare('SELECT COUNT(*) FROM usuarios WHERE login = :login OR email = :email');
            $stmt->bindParam(':login', $this->login);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                $stmt = $conexion->prepare('INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos, email, fecha_registro, activo) 
                                          VALUES (:login, :clave, :id_rol, :nombre, :apellidos, :email, :fecha_registro, :activo)');
                
                $claveCifrada = hash('sha512', $this->clave);
                $stmt->bindParam(':login', $this->login);
                $stmt->bindParam(':clave', $claveCifrada);
                $stmt->bindParam(':id_rol', $this->id_rol);
                $stmt->bindParam(':nombre', $this->nombre);
                $stmt->bindParam(':apellidos', $this->apellidos);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':fecha_registro', $this->fecha_registro);
                $stmt->bindParam(':activo', $this->activo);
                
                $exito = $stmt->execute();
                $conexion->commit();
            }
            
        } catch (PDOException $e) {
            $conexion->rollBack();
        }
        
        return $exito;
    }

    public function actualizarUsuario() {
        $conexion = Db::getConexion();
        $resultado = false;
        
        try {
            $conexion->beginTransaction();
            
            $sql = 'UPDATE usuarios SET 
                    clave = :clave,
                    id_rol = :id_rol,
                    nombre = :nombre,
                    apellidos = :apellidos,
                    email = :email,
                    activo = :activo
                    WHERE login = :login';
            
            $stmt = $conexion->prepare($sql);
            
            $claveCifrada = hash('sha512', $this->clave);
            $stmt->bindParam(':clave', $claveCifrada);
            $stmt->bindParam(':id_rol', $this->id_rol);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':activo', $this->activo);
            $stmt->bindParam(':login', $this->login);
            
            $resultado = $stmt->execute();
            $conexion->commit();
            
        } catch (PDOException $e) {
            $conexion->rollBack();
        }
        
        return $resultado;
    }

    public function eliminarUsuario() {
        $conexion = Db::getConexion();
        $resultado = false;
        try {
            $conexion->beginTransaction();
            
            $sql = 'DELETE FROM usuarios WHERE login = :login';
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':login', $this->login);
            
            $resultado = $stmt->execute();
            $conexion->commit();
            
        } catch (PDOException $e) {
            $conexion->rollBack();
        }
        return $resultado;
    }

    public static function autenticarUsuario($login, $clave) {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE login = :login AND clave = :clave AND activo = true');
        
        $claveCifrada = hash('sha512', $clave);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':clave', $claveCifrada);
        
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        return $stmt->fetch();
    }
}
