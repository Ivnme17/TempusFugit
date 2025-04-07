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
}
