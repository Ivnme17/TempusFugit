<?php
require_once '../Servicio/Db.php';
class Reloj {
    private $id_reloj;
    private $id_marca_modelo;
    private $precio;
    private $tipo;
    private $stock;
    private $id_usuario;
    private $url_imagen;
    
    private $marca;
    private $modelo;
    
    public function __construct($datos = null) {
        if ($datos) {
            $this->id_reloj = isset($datos['id_reloj']) ? $datos['id_reloj'] : null;
            $this->id_marca_modelo = isset($datos['id_marca_modelo']) ? $datos['id_marca_modelo'] : 0;
            $this->precio = isset($datos['precio']) ? (float)$datos['precio'] : 0.00;
            $this->tipo = isset($datos['tipo']) ? $datos['tipo'] : 'analógico';
            $this->stock = isset($datos['stock']) ? (int)$datos['stock'] : 0;
            $this->id_usuario = isset($datos['id_usuario']) ? $datos['id_usuario'] : null;
            $this->url_imagen = isset($datos['url_imagen']) ? $datos['url_imagen'] : 'img/no-image.jpg';
            
            $this->marca = isset($datos['marca']) ? $datos['marca'] : null;
            $this->modelo = isset($datos['modelo']) ? $datos['modelo'] : null;
        } else {
            $this->id_reloj = null;
            $this->id_marca_modelo = 0;
            $this->precio = 0.00;
            $this->tipo = 'analógico';
            $this->stock = 0;
            $this->id_usuario = null;
            $this->url_imagen = 'img/no-image.jpg';
            $this->marca = null;
            $this->modelo = null;
        }
    }
    
    public function getId() { 
        return $this->id_reloj; 
    }
    
    public function getIdMarcaModelo() { 
        return $this->id_marca_modelo; 
    }
    
    public function getPrecio() {
        return $this->precio; 
    }
    
    public function getTipo() { 
        return $this->tipo; 
    }
    
    public function getStock() {
        return $this->stock;
    }
    
    public function getIdUsuario() { 
        return $this->id_usuario; 
    }
    
    public function getUrlImagen() { 
        return $this->url_imagen; 
    }
    
    public function getMarca() {
        return $this->marca;
    }
    
    public function getModelo() {
        return $this->modelo;
    }
    
    public function setId($id) { 
        $this->id_reloj = $id; 
    }
    
    public function setIdMarcaModelo($id_marca_modelo) { 
        $this->id_marca_modelo = $id_marca_modelo; 
    }
    
    public function setPrecio($precio) { 
        $this->precio = $precio;
    }
    
    public function setTipo($tipo) { 
        if ($tipo === 'digital' || $tipo === 'analógico') {
            $this->tipo = $tipo;
        } else {
            throw new InvalidArgumentException("El tipo debe ser 'digital' o 'analógico'");
        }
    }
    
    public function setStock($stock) { 
        $this->stock = $stock; 
    }
    
    public function setIdUsuario($id_usuario) { 
        $this->id_usuario = $id_usuario; 
    }
    
    public function setUrlImagen($url_imagen) { 
        $this->url_imagen = $url_imagen; 
    }
    
    public function setMarca($marca) {
        $this->marca = $marca;
    }
    
    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }
    
    public function getNombreCompleto() {
        if ($this->marca && $this->modelo) {
            return "{$this->marca} {$this->modelo}";
        }
        
        if ($this->id_marca_modelo > 0) {
            $this->cargarDatosMarcaModelo();
            return "{$this->marca} {$this->modelo}";
        }
        
        return "Reloj #" . ($this->id_reloj ?? 'nuevo');
    }
    
    private function cargarDatosMarcaModelo() {
        if (!$this->id_marca_modelo) {
            return;
        }
        
        $conexion = Db::getConexion();
        $consulta = "SELECT marca, modelo FROM marca_modelo WHERE id_marca_modelo = :id_marca_modelo";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':id_marca_modelo', $this->id_marca_modelo, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $this->marca = $resultado['marca'];
            $this->modelo = $resultado['modelo'];
        }
    }
    
    public function getPrecioFormateado() {
        return number_format($this->precio, 2, ',', '.') . ' €';
    }
    
    public function guardar() {
        $conexion = Db::getConexion();
        
        if ($this->id_reloj !== null) {
            $consulta = "UPDATE relojes SET 
                    id_marca_modelo = :id_marca_modelo,
                    precio = :precio,
                    tipo = :tipo,
                    stock = :stock,
                    id_usuario = :id_usuario,
                    url_imagen = :url_imagen
                    WHERE id_reloj = :id_reloj";
        } else {
            $consulta = "INSERT INTO relojes 
                    (id_marca_modelo, precio, tipo, stock, id_usuario, url_imagen) 
                    VALUES 
                    (:id_marca_modelo, :precio, :tipo, :stock, :id_usuario, :url_imagen)";
        }
        
        $stmt = $conexion->prepare($consulta);
        
        if ($this->id_reloj !== null) {
            $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':id_marca_modelo', $this->id_marca_modelo, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':tipo', $this->tipo, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':url_imagen', $this->url_imagen, PDO::PARAM_STR);
        
        $resultado = $stmt->execute();
        
        if ($this->id_reloj === null && $resultado) {
            $this->id_reloj = (int)$conexion->lastInsertId();
        }
        
        return $resultado;
    }
    
    public function eliminar() {
        if ($this->id_reloj === null) {
            return false;
        }
        
        $conexion = Db::getConexion();
        $consulta = "DELETE FROM relojes WHERE id_reloj = :id_reloj";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public static function obtenerTodos() {
        $conexion = Db::getConexion();
        $consulta = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                ORDER BY mm.marca, mm.modelo";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute();
        
        $relojes = [];
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $fila) {
            $relojes[] = new Reloj($fila);
        }
        
        return $relojes;
    }
    
    public static function obtenerPorMarca($marca) {
        $conexion = Db::getConexion();
        $consulta = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                WHERE mm.marca = :marca 
                ORDER BY mm.modelo";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->execute();
        
        $relojes = [];
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $fila) {
            $relojes[] = new Reloj($fila);
        }
        
        return $relojes;
    }
    
    public static function obtenerPorId($id) {
        $conexion = Db::getConexion();
        $consulta = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                WHERE r.id_reloj = :id";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fila ? new Reloj($fila) : false;
    }
    
    
    public static function obtenerPorNombre($nombre) {
        $conexion = Db::getConexion();
        $consulta = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                WHERE CONCAT(mm.marca, ' ', mm.modelo) = :nombre";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fila ? new Reloj($fila) : false;
    }

    public static function obtenerMarcas() {
        $conexion = Db::getConexion();
        $consulta = "SELECT DISTINCT marca FROM marca_modelo ORDER BY marca";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public static function obtenerModelosPorMarca($marca) {
        $conexion = Db::getConexion();
        $consulta = "SELECT id_marca_modelo, modelo 
                FROM marca_modelo 
                WHERE marca = :marca 
                ORDER BY modelo";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function disponible() {
        return $this->stock > 0;
    }
    
    public function reducirStock($cantidad = 1) {
        if ($this->stock < $cantidad) {
            return false;
        }
        
        $this->stock -= $cantidad;
        return $this->guardar();
    }
    
    public function aumentarStock($cantidad = 1) {
        $this->stock += $cantidad;
        return $this->guardar();
    }
}