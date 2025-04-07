<?php
require_once './Servicio/Db.php';

class Usuario {
    private $id_usuario;
    private $login;
    private $clave;
    private $id_rol;
    private $nombre;
    private $apellidos;
    private $dni;
    private $nss;
    private $telefono;
    private $correo;
    private $direccion;
    private $iban;
    
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
    
    public function getDni() {
        return $this->dni;
    }
    
    public function setDni($dni): void {
        $this->dni = trim($dni);
    }
    
    public function getNss() {
        return $this->nss;
    }
    
    public function setNss($nss): void {
        $this->nss = trim($nss);
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    public function setTelefono($telefono): void {
        $this->telefono = trim($telefono);
    }
    
    public function getCorreo() {
        return $this->correo;
    }
    
    public function setCorreo($correo): void {
        $this->correo = trim($correo);
    }
    
    public function getDireccion() {
        return $this->direccion;
    }
    
    public function setDireccion($direccion): void {
        $this->direccion = trim($direccion);
    }
    
    public function getIban() {
        return $this->iban;
    }
    
    public function setIban($iban): void {
        $this->iban = trim($iban);
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
                $stmt = $conexion->prepare('INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos, dni, nss, telefono, correo, direccion, iban) 
                                          VALUES (:login, :clave, :id_rol, :nombre, :apellidos, :dni, :nss, :telefono, :correo, :direccion, :iban)');
                
                $claveCifrada = hash('sha512', $this->clave);
                $stmt->bindParam(':login', $this->login);
                $stmt->bindParam(':clave', $claveCifrada);
                $stmt->bindParam(':id_rol', $this->id_rol);
                $stmt->bindParam(':nombre', $this->nombre);
                $stmt->bindParam(':apellidos', $this->apellidos);
                $stmt->bindParam(':dni', $this->dni);
                $stmt->bindParam(':nss', $this->nss);
                $stmt->bindParam(':telefono', $this->telefono);
                $stmt->bindParam(':correo', $this->correo);
                $stmt->bindParam(':direccion', $this->direccion);
                $stmt->bindParam(':iban', $this->iban);
                
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
                    dni = :dni,
                    nss = :nss,
                    telefono = :telefono,
                    correo = :correo,
                    direccion = :direccion,
                    iban = :iban
                    WHERE id_usuario = :id_usuario';
            
            $stmt = $conexion->prepare($sql);
            
            $claveCifrada = hash('sha512', $this->clave);
            $stmt->bindParam(':clave', $claveCifrada);
            $stmt->bindParam(':id_rol', $this->id_rol);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':dni', $this->dni);
            $stmt->bindParam(':nss', $this->nss);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':direccion', $this->direccion);
            $stmt->bindParam(':iban', $this->iban);
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