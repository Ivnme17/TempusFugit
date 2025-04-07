<?php
require_once './Servicio/Db.php';

class Usuario {
    private $login;
    private $clave;
    private $id_rol;
    private $nombre;
    private $email;
    private $fecha_registro;
    private $activo;
    
    public function __construct() {
        $this->fecha_registro = date('Y-m-d H:i:s');
        $this->activo = true;
    }
    
    // Getters
    public function getLogin() { 
        return $this->login; 
    }
    public function getClave() { 
        return $this->clave; 
    }
    public function getId_rol() { 
        return $this->id_rol; 
    }
    public function getNombre() {
         return $this->nombre; 
        }
    public function getEmail() { 
        return $this->email;
     }
    public function getFechaRegistro() { 
        return $this->fecha_registro; 
    }
    public function getActivo() { 
        return $this->activo; 
    }

    // Setters
    public function setLogin($login): void { 
        $this->login = trim($login); 
    }
    public function setClave($nuevaClave): void { 
        $this->clave = $nuevaClave; 
    }
    public function setId_rol($id_rol): void { 
        $this->id_rol = $id_rol; 
    }
    public function setNombre($nombre): void { 
        $this->nombre = trim($nombre); 
    }
    public function setEmail($email): void { 
        $this->email = trim($email); 
    }
    public function setActivo($activo): void { 
        $this->activo = $activo; 
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
                $stmt = $conexion->prepare('INSERT INTO usuarios (login, clave, id_rol, nombre, email, fecha_registro, activo) 
                                          VALUES (:login, :clave, :id_rol, :nombre, :email, :fecha_registro, :activo)');
                
                $claveCifrada = hash('sha512', $this->clave);
                $stmt->bindParam(':login', $this->login);
                $stmt->bindParam(':clave', $claveCifrada);
                $stmt->bindParam(':id_rol', $this->id_rol);
                $stmt->bindParam(':nombre', $this->nombre);
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
                    email = :email,
                    activo = :activo
                    WHERE login = :login';
            
            $stmt = $conexion->prepare($sql);
            
            $claveCifrada = hash('sha512', $this->clave);
            $stmt->bindParam(':clave', $claveCifrada);
            $stmt->bindParam(':id_rol', $this->id_rol);
            $stmt->bindParam(':nombre', $this->nombre);
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

    public static function obtenerTodosUsuarios() {
        $conexion = Db::getConexion();
        $usuarios = [];
        
        try {
            $stmt = $conexion->query('SELECT * FROM usuarios');
            $usuarios = $stmt->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error: " . $e->getMessage();
        }
        
        return $usuarios;
    }

    public static function obtenerUsuarioPorId($login) {
        $conexion = Db::getConexion();
        $usuario = null;
        
        try {
            $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE login = :login');
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
            $usuario = $stmt->fetch();
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error: " . $e->getMessage();
        }
        
        return $usuario;
    }
}
