<?php
require_once './Servicio/Db.php';

class Usuario{

    private $login;
    private $clave;
    private $id_rol;
    
    
    public function __construct() {

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

    public function setLogin($login): void {
        $this->login = $login;
    }

    public function setClave($nuevaClave): void {
        $this->clave = $nuevaClave;
    }

    public function setId_rol($id_rol): void {
        $this->id_rol = $id_rol;
    }


    public function añadirUsuario(){
        $conexion = Db::getConexion();
        $esInsertado = false;
        $clave = $this->clave;
        $claveCifrada = hash('sha512',$clave);
        $login = $this->login;
        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE login = :login');
        $stmt->bindParam(':login',$login);
        $stmt->execute();
        $r = $stmt->fetchColumn();
        
        if(!$r){
            $stmt = $conexion->prepare('INSERT INTO usuarios (login,clave,id_rol) VALUES (:login,:clave,:id_rol)');
            $idRol = $this->id_rol;
            $stmt->bindParam(':login',$login);
            $stmt->bindParam(':clave',$claveCifrada);
            $stmt->bindParam(':id_rol',$idRol);
            $stmt->execute();
            $esInsertado = true;
        }else{
            $esInsertado = false;
        }
        return $esInsertado;
    }
    
    
    public function actualizarUsuario(){
        $conexion = Db::getConexion();
        $esActualizado = false;
        $clave = $this->clave;
        $claveEncriptada = hash('sha512',$clave);
        $login = $this->login;
        $stmt = $conexion->prepare('SELECT login FROM usuarios WHERE login = :login');
        $stmt->bindParam(':login',$login);
        $stmt->execute();
        $r = $stmt->fetchColumn();
        
        if($r){
            $stmt = $conexion->prepare('UPDATE usuarios SET clave = :clave, id_rol = :id_rol');
            $idRol = $this->id_rol;
            $stmt->bindParam(':clave',$claveEncriptada);
            $stmt->bindParam('id_rol',$idRol);
            $stmt->execute();
            $stmt->closeCursor();
            $esActualizado = true;
        }else{
            $esActualizado = false;
        }
        return $esActualizado;
    }
    
    
    public static function eliminarUsuario($login){
        $conexion = Db::getConexion();
        $stmt = $conexion->prepare('DELETE FROM usuario WHERE login = :login');
        $stmt->bindParam(':login',$login);
        $stmt->execute();
        $esEliminado = true;
        
        return $esEliminado;
    }
    
    public static function verUsuario($login) {
        try {
            $conexion = Db::getConexion(); // Conectar a la base de datos
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
            $usuarios = $stmt->fetchAll(PDO::FETCH_CLASS,self::class);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $usuarios = false;
        }
        return $usuarios;
    }
    
    public static function autenticarUsuario($nombreLogin, $passwd){
        $conexion = Db::getConexion();
        $clave = hash("sha512", $passwd);
        // $sql = <<<q1
        //select login
        //from usuarios
        //where login = :login and clave = :clave
        //q1;
        $consultaPreparada = $conexion->prepare("SELECT * FROM usuarios WHERE login = :login and clave = :clave");
        $consultaPreparada->bindParam(":login",$nombreLogin);
        $consultaPreparada->bindParam(":clave",$clave);
        $consultaPreparada->execute();
        $usuario = $consultaPreparada->fetchObject(self::class);
        return $usuario;
    }
    
    
}