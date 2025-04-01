<?php
/**
 * Clase que representa un reloj en el sistema
 */
class Reloj {
    private $id_reloj;
    private $marca;
    private $modelo;
    private $precio;
    private $tipo;
    private $disponibilidad;
    private $id_usuario;
    private $url_imagen;
    
    /**
     * Constructor de la clase Reloj
     * 
     * @param array $datos Datos del reloj (opcional)
     */
    public function __construct($datos = null) {
        if ($datos) {
            $this->id_reloj = isset($datos['ID_reloj']) ? $datos['ID_reloj'] : null;
            $this->marca = isset($datos['marca']) ? $datos['marca'] : '';
            $this->modelo = isset($datos['modelo']) ? $datos['modelo'] : '';
            $this->precio = isset($datos['precio']) ? $datos['precio'] : 0.00;
            $this->tipo = isset($datos['tipo']) ? $datos['tipo'] : 'analógico';
            $this->disponibilidad = isset($datos['disponibilidad']) ? (bool)$datos['disponibilidad'] : true;
            $this->id_usuario = isset($datos['ID_Usuario']) ? $datos['ID_Usuario'] : null;
            $this->url_imagen = isset($datos['url_imagen']) ? $datos['url_imagen'] : 'img/no-image.jpg';
        }
    }
    
    // Getters
    public function getId() { return $this->id_reloj; }
    public function getMarca() { return $this->marca; }
    public function getModelo() { return $this->modelo; }
    public function getPrecio() { return $this->precio; }
    public function getTipo() { return $this->tipo; }
    public function getDisponibilidad() { return $this->disponibilidad; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function getUrlImagen() { return $this->url_imagen; }
    
    // Setters
    public function setId($id) { 
        $this->id_reloj = $id; 
    }
    public function setMarca($marca) { 
        $this->marca = $marca; 
    }
    public function setModelo($modelo) { 
        $this->modelo = $modelo; 
    }
    public function setPrecio($precio) { 
        $this->precio = $precio;
     }
    public function setTipo($tipo) { 
        $this->tipo = $tipo;
     }
    public function setDisponibilidad($disponibilidad) { 
        $this->disponibilidad = (bool)$disponibilidad; 
    }
    public function setIdUsuario($id_usuario) { 
        $this->id_usuario = $id_usuario; 
    }
    public function setUrlImagen($url_imagen) { 
        $this->url_imagen = $url_imagen; 
    }
    
    /**
     * Obtiene el nombre completo del reloj (marca + modelo)
     * 
     * @return string
     */
    public function getNombreCompleto() {
        return "{$this->marca} {$this->modelo}";
    }
    
    /**
     * Formatea el precio para mostrar
     * 
     * @return string
     */
    public function getPrecioFormateado() {
        return number_format($this->precio, 2, ',', '.') . ' €';
    }
    
    /**
     * Guarda o actualiza un reloj en la base de datos
     * 
     * @return boolean
     */
    public function guardar() {
        try {
            $conn = Db::getConexion();
            
            // Si tiene ID, actualiza; si no, inserta
            if ($this->id_reloj) {
                $sql = "UPDATE Relojes SET 
                        marca = :marca,
                        modelo = :modelo,
                        precio = :precio,
                        tipo = :tipo,
                        disponibilidad = :disponibilidad,
                        ID_Usuario = :id_usuario,
                        url_imagen = :url_imagen
                        WHERE ID_reloj = :id_reloj";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO Relojes 
                        (marca, modelo, precio, tipo, disponibilidad, ID_Usuario, url_imagen) 
                        VALUES 
                        (:marca, :modelo, :precio, :tipo, :disponibilidad, :id_usuario, :url_imagen)";
                
                $stmt = $conn->prepare($sql);
            }
            
            // Bind de parámetros comunes
            $stmt->bindParam(':marca', $this->marca, PDO::PARAM_STR);
            $stmt->bindParam(':modelo', $this->modelo, PDO::PARAM_STR);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':tipo', $this->tipo, PDO::PARAM_STR);
            $disponibilidad = $this->disponibilidad ? 1 : 0;
            $stmt->bindParam(':disponibilidad', $disponibilidad, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':url_imagen', $this->url_imagen, PDO::PARAM_STR);
            
            $resultado = $stmt->execute();
            
            // Si es una inserción, obtenemos el ID generado
            if (!$this->id_reloj && $resultado) {
                $this->id_reloj = $conn->lastInsertId();
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al guardar reloj: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un reloj de la base de datos
     * 
     * @return boolean
     */
    public function eliminar() {
        if (!$this->id_reloj) {
            return false;
        }
        
        try {
            $conn = Db::getConexion();
            $sql = "DELETE FROM Relojes WHERE ID_reloj = :id_reloj";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar reloj: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todos los relojes de la base de datos
     * 
     * @return array
     */
    public static function obtenerTodos() {
        try {
            $conn = Db::getConexion();
            $sql = "SELECT * FROM Relojes ORDER BY marca, modelo";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            $relojes = [];
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultados as $fila) {
                $relojes[] = new Reloj($fila);
            }
            
            return $relojes;
        } catch (PDOException $e) {
            error_log("Error al obtener relojes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene relojes por marca
     * 
     * @param string $marca
     * @return array
     */
    public static function obtenerPorMarca($marca) {
        try {
            $conn = Db::getConexion();
            $sql = "SELECT * FROM Relojes WHERE marca = :marca ORDER BY modelo";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
            $stmt->execute();
            
            $relojes = [];
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultados as $fila) {
                $relojes[] = new Reloj($fila);
            }
            
            return $relojes;
        } catch (PDOException $e) {
            error_log("Error al obtener relojes por marca: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un reloj por su ID
     * 
     * @param int $id
     * @return Reloj|null
     */
    public static function obtenerPorId($id) {
        try {
            $conn = Db::getConexion();
            $sql = "SELECT * FROM Relojes WHERE ID_reloj = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($fila) {
                return new Reloj($fila);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error al obtener reloj por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtiene las marcas disponibles
     * 
     * @return array
     */
    public static function obtenerMarcas() {
        try {
            $conn = Db::getConexion();
            $sql = "SELECT DISTINCT marca FROM Relojes ORDER BY marca";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error al obtener marcas: " . $e->getMessage());
            return [];
        }
    }
}
?>