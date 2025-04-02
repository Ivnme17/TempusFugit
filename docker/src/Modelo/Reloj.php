<?php
/**
 * Clase que representa un reloj en el sistema
 */
class Reloj {
    // Using typed properties (PHP 7.4+)
    private ?int $id_reloj;
    private string $marca;
    private string $modelo;
    private float $precio;
    private string $tipo;
    private bool $disponibilidad;
    private ?int $id_usuario;
    private string $url_imagen;
    
    /**
     * Constructor de la clase Reloj
     * 
     * @param array|null $datos Datos del reloj (opcional)
     */
    public function __construct(?array $datos = null) {
        // Using null coalescing operator and type casting where appropriate
        if ($datos) {
            $this->id_reloj = $datos['ID_reloj'] ?? null;
            $this->marca = $datos['marca'] ?? '';
            $this->modelo = $datos['modelo'] ?? '';
            $this->precio = (float)($datos['precio'] ?? 0.00);
            $this->tipo = $datos['tipo'] ?? 'analógico';
            $this->disponibilidad = (bool)($datos['disponibilidad'] ?? true);
            $this->id_usuario = $datos['ID_Usuario'] ?? null;
            $this->url_imagen = $datos['url_imagen'] ?? 'img/no-image.jpg';
        } else {
            // Initialize properties when no data is provided
            $this->id_reloj = null;
            $this->marca = '';
            $this->modelo = '';
            $this->precio = 0.00;
            $this->tipo = 'analógico';
            $this->disponibilidad = true;
            $this->id_usuario = null;
            $this->url_imagen = 'img/no-image.jpg';
        }
    }
    
    // Getters without return type declarations
    public function getId() { 
        return $this->id_reloj; 
    }
    
    public function getMarca() { 
        return $this->marca; 
    }
    
    public function getModelo() { 
        return $this->modelo; 
    }
    
    public function getPrecio() {
        return $this->precio; 
    }
    
    public function getTipo() { 
        return $this->tipo; 
    }
    
    public function getDisponibilidad() {
        return $this->disponibilidad;
    }
    
    public function getIdUsuario() { 
        return $this->id_usuario; 
    }
    
    public function getUrlImagen() { 
        return $this->url_imagen; 
    }
    
    // Setters with void return type declarations
    public function setId(?int $id): void { 
        $this->id_reloj = $id; 
    }
    
    public function setMarca(string $marca): void { 
        $this->marca = $marca; 
    }
    
    public function setModelo(string $modelo): void { 
        $this->modelo = $modelo; 
    }
    
    public function setPrecio(float $precio): void { 
        $this->precio = $precio;
    }
    
    public function setTipo(string $tipo): void { 
        $this->tipo = $tipo;
    }
    
    public function setDisponibilidad(bool $disponibilidad): void { 
        $this->disponibilidad = $disponibilidad; 
    }
    
    public function setIdUsuario(?int $id_usuario): void { 
        $this->id_usuario = $id_usuario; 
    }
    
    public function setUrlImagen(string $url_imagen): void { 
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
     * @return bool
     * @throws PDOException
     */
    public function guardar() {
        $conn = Db::getConexion();
        
        // Using match expression (PHP 8.0+) for more concise conditional logic
        $sql = match(true) {
            $this->id_reloj !== null => "UPDATE Relojes SET 
                    marca = :marca,
                    modelo = :modelo,
                    precio = :precio,
                    tipo = :tipo,
                    disponibilidad = :disponibilidad,
                    ID_Usuario = :id_usuario,
                    url_imagen = :url_imagen
                    WHERE ID_reloj = :id_reloj",
            default => "INSERT INTO Relojes 
                    (marca, modelo, precio, tipo, disponibilidad, ID_Usuario, url_imagen) 
                    VALUES 
                    (:marca, :modelo, :precio, :tipo, :disponibilidad, :id_usuario, :url_imagen)"
        };
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters conditionally
        if ($this->id_reloj !== null) {
            $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
        }
        
        // Using named arguments (PHP 8.0+) for parameter binding
        $disponibilidad = (int)$this->disponibilidad;
        $stmt->bindParam(param: ':marca', var: $this->marca, type: PDO::PARAM_STR);
        $stmt->bindParam(param: ':modelo', var: $this->modelo, type: PDO::PARAM_STR);
        $stmt->bindParam(param: ':precio', var: $this->precio);
        $stmt->bindParam(param: ':tipo', var: $this->tipo, type: PDO::PARAM_STR);
        $stmt->bindParam(param: ':disponibilidad', var: $disponibilidad, type: PDO::PARAM_INT);
        $stmt->bindParam(param: ':id_usuario', var: $this->id_usuario, type: PDO::PARAM_INT);
        $stmt->bindParam(param: ':url_imagen', var: $this->url_imagen, type: PDO::PARAM_STR);
        
        $resultado = $stmt->execute();
        
        // Update ID if it was an insertion
        if ($this->id_reloj === null && $resultado) {
            $this->id_reloj = (int)$conn->lastInsertId();
        }
        
        return $resultado;
    }
    
    /**
     * Elimina un reloj de la base de datos
     * 
     * @return bool
     * @throws PDOException
     */
    public function eliminar() {
        if ($this->id_reloj === null) {
            return false;
        }
        
        $conn = Db::getConexion();
        $sql = "DELETE FROM Relojes WHERE ID_reloj = :id_reloj";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Obtiene todos los relojes de la base de datos
     * 
     * @return array<Reloj>
     * @throws PDOException
     */
    public static function obtenerTodos() {
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
    }
    
    /**
     * Obtiene relojes por marca
     * 
     * @param string $marca
     * @return array<Reloj>
     * @throws PDOException
     */
    public static function obtenerPorMarca(string $marca) {
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
    }
    
    /**
     * Obtiene un reloj por su ID
     * 
     * @param int $id
     * @return Reloj|null
     * @throws PDOException
     */
    public static function obtenerPorId(int $id) {
        $conn = Db::getConexion();
        $sql = "SELECT * FROM Relojes WHERE ID_reloj = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fila ? new Reloj($fila) : null;
    }
    
    /**
     * Obtiene las marcas disponibles
     * 
     * @return array<string>
     * @throws PDOException
     */
    public static function obtenerMarcas() {
        $conn = Db::getConexion();
        $sql = "SELECT DISTINCT marca FROM Relojes ORDER BY marca";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}