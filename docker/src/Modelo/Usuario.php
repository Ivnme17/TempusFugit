<?php
require_once './Servicio/Db.php';

class Usuario {
    private $id_usuario;
    private $login;
    private $clave;
    private $id_rol;
    private $nombre;
    private $apellidos;
    
    public function __construct() {
    }
    
    public function getId_usuario() {
        return $this->id_usuario;
    }

    public function setId_usuario($id_usuario): void {
        $this->id_usuario = $id_usuario;
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
    
    public function getApellidos() {
        return $this->apellidos;
    }
    
    public function setApellidos($apellidos): void {
        $this->apellidos = trim($apellidos);
    }

    public static function obtenerUsuarioPorId($id) {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE id_usuario = :id');
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
            
            $stmt = $conexion->prepare('SELECT COUNT(*) FROM usuarios WHERE login = :login');
            $stmt->bindParam(':login', $this->login);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                $stmt = $conexion->prepare('INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos) 
                                          VALUES (:login, :clave, :id_rol, :nombre, :apellidos)');
                
                $claveCifrada = hash('sha512', $this->clave);
                $stmt->bindParam(':login', $this->login);
                $stmt->bindParam(':clave', $claveCifrada);
                $stmt->bindParam(':id_rol', $this->id_rol);
                $stmt->bindParam(':nombre', $this->nombre);
                $stmt->bindParam(':apellidos', $this->apellidos);
                
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
                    apellidos = :apellidos
                    WHERE id_usuario = :id_usuario';
            
            $stmt = $conexion->prepare($sql);
            
            $claveCifrada = hash('sha512', $this->clave);
            $stmt->bindParam(':clave', $claveCifrada);
            $stmt->bindParam(':id_rol', $this->id_rol);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':id_usuario', $this->id_usuario);
            
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
            
            $sql = 'DELETE FROM usuarios WHERE id_usuario = :id_usuario';
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $this->id_usuario);
            
            $resultado = $stmt->execute();
            $conexion->commit();
            
        } catch (PDOException $e) {
            $conexion->rollBack();
        }
        return $resultado;
    }

    public static function autenticarUsuario($login, $clave) {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE login = :login AND clave = :clave');
        
        $claveCifrada = hash('sha512', $clave);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':clave', $claveCifrada);
        
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        return $stmt->fetch();
    }
}
