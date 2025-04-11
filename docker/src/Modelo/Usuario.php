<?php
require_once __DIR__ . '/../Servicio/Db.php';
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
        // Constructor vacío
    }
    
    // Getters
    public function getId_usuario() {
        return $this->id_usuario;
    }
    
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
    
    public function getApellidos() {
        return $this->apellidos;
    }
    
    public function getDni() {
        return $this->dni;
    }
    
    public function getNss() {
        return $this->nss;
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    public function getCorreo() {
        return $this->correo;
    }
    
    public function getDireccion() {
        return $this->direccion;
    }
    
    public function getIban() {
        return $this->iban;
    }
    
    // Setters
    public function setId_usuario($id_usuario): void {
        $this->id_usuario = $id_usuario;
    }
    
    public function setLogin($login): void {
        $this->login = $login;
    }

    public function setClave($nuevaClave): void {
        $this->clave = $nuevaClave;
    }

    public function setId_rol($id_rol): void {
        $this->id_rol = $id_rol;
    }
    
    public function setNombre($nombre): void {
        $this->nombre = $nombre;
    }
    
    public function setApellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }
    
    public function setDni($dni): void {
        $this->dni = $dni;
    }
    
    public function setNss($nss): void {
        $this->nss = $nss;
    }
    
    public function setTelefono($telefono): void {
        $this->telefono = $telefono;
    }
    
    public function setCorreo($correo): void {
        $this->correo = $correo;
    }
    
    public function setDireccion($direccion): void {
        $this->direccion = $direccion;
    }
    
    public function setIban($iban): void {
        $this->iban = $iban;
    }

    public function añadirUsuario() {
        $conexion = Db::getConexion();
        $esInsertado = false;
        $clave = $this->clave;
        $claveCifrada = hash('sha512', $clave);
        $login = $this->login;
        
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE login = :login');
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $r = $stmt->fetchColumn();
        
        if (!$r) {
            $stmt = $conexion->prepare('INSERT INTO usuarios (login, clave, id_rol, nombre, apellidos, dni, nss, telefono, correo, direccion, iban) 
                                     VALUES (:login, :clave, :id_rol, :nombre, :apellidos, :dni, :nss, :telefono, :correo, :direccion, :iban)');
            
            $stmt->bindParam(':login', $login);
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
            
            $stmt->execute();
            $esInsertado = true;
        }
        
        return $esInsertado;
    }
    
    public function actualizarUsuario() {
        $conexion = Db::getConexion();
        $esActualizado = false;
        $clave = $this->clave;
        $claveEncriptada = hash('sha512', $clave);
        $login = $this->login;
        
        $stmt = $conexion->prepare('SELECT login FROM usuarios WHERE login = :login');
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $r = $stmt->fetchColumn();
        
        if ($r) {
            $stmt = $conexion->prepare('UPDATE usuarios SET 
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
                                      WHERE login = :login');
            
            $stmt->bindParam(':clave', $claveEncriptada);
            $stmt->bindParam(':id_rol', $this->id_rol);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':dni', $this->dni);
            $stmt->bindParam(':nss', $this->nss);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':direccion', $this->direccion);
            $stmt->bindParam(':iban', $this->iban);
            $stmt->bindParam(':login', $login);
            
            $stmt->execute();
            $stmt->closeCursor();
            $esActualizado = true;
        }
        
        return $esActualizado;
    }
    
    public static function eliminarUsuario($login) {
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('DELETE FROM usuarios WHERE login = :login');
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $esEliminado = true;
        
        return $esEliminado;
    }
    
    public static function verUsuario($login) {
        try {
            $conexion = Db::getConexion();
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE login = :login");
            $consulta->bindParam(":login", $login, PDO::PARAM_STR);
            $consulta->execute();

            $usuario = $consulta->fetchObject(self::class);
            $consulta->closeCursor();

        } catch (PDOException $e) {
            $usuario = false;
        }
        return $usuario;
    }
    
    public static function listarUsuarios() {
        try {
            $conexion = Db::getConexion();
            $stmt = $conexion->prepare("SELECT * FROM usuarios");
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $usuarios = false;
        }
        return $usuarios;
    }

    
    public static function autenticarUsuario($nombreLogin, $passwd) {
        $conexion = Db::getConexion();
        $clave = hash("sha512", $passwd);
        
        $consultaPreparada = $conexion->prepare("SELECT * FROM usuarios WHERE login = :login and clave = :clave");
        $consultaPreparada->bindParam(":login", $nombreLogin);
        $consultaPreparada->bindParam(":clave", $clave);
        $consultaPreparada->execute();
        $usuario = $consultaPreparada->fetchObject(self::class);
        return $usuario;
    }
    
    public static function buscarUsuarios($criterio, $valor) {
        try {
            $conexion = Db::getConexion();
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE $criterio LIKE :valor");
            $valorBusqueda = "%$valor%";
            $stmt->bindParam(":valor", $valorBusqueda);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $usuarios = false;
        }
        return $usuarios;
    }

    public static function listarClientes() {
        try {
            $conexion = Db::getConexion();
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_rol = 3");
            $stmt->execute();
            $clientes = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $clientes = false;
        }
        return $clientes;
    }
}