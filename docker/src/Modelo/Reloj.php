<?php
class Reloj {
    private ?int $id_reloj;
    private int $id_marca_modelo;
    private float $precio;
    private string $tipo;
    private int $stock;
    private ?int $id_usuario;
    private string $url_imagen;
    
    private ?string $marca;
    private ?string $modelo;
    
    public function __construct(?array $datos = null) {
        if ($datos) {
            $this->id_reloj = $datos['id_reloj'] ?? null;
            $this->id_marca_modelo = $datos['id_marca_modelo'] ?? 0;
            $this->precio = (float)($datos['precio'] ?? 0.00);
            $this->tipo = $datos['tipo'] ?? 'analógico';
            $this->stock = (int)($datos['stock'] ?? 0);
            $this->id_usuario = $datos['id_usuario'] ?? null;
            $this->url_imagen = $datos['url_imagen'] ?? 'img/no-image.jpg';
            
            $this->marca = $datos['marca'] ?? null;
            $this->modelo = $datos['modelo'] ?? null;
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
    
    public function getId(): ?int { 
        return $this->id_reloj; 
    }
    
    public function getIdMarcaModelo(): int { 
        return $this->id_marca_modelo; 
    }
    
    public function getPrecio(): float {
        return $this->precio; 
    }
    
    public function getTipo(): string { 
        return $this->tipo; 
    }
    
    public function getStock(): int {
        return $this->stock;
    }
    
    public function getIdUsuario(): ?int { 
        return $this->id_usuario; 
    }
    
    public function getUrlImagen(): string { 
        return $this->url_imagen; 
    }
    
    public function getMarca(): ?string {
        return $this->marca;
    }
    
    public function getModelo(): ?string {
        return $this->modelo;
    }
    
    public function setId(?int $id): void { 
        $this->id_reloj = $id; 
    }
    
    public function setIdMarcaModelo(int $id_marca_modelo): void { 
        $this->id_marca_modelo = $id_marca_modelo; 
    }
    
    public function setPrecio(float $precio): void { 
        $this->precio = $precio;
    }
    
    public function setTipo(string $tipo): void { 
        if ($tipo === 'digital' || $tipo === 'analógico') {
            $this->tipo = $tipo;
        } else {
            throw new InvalidArgumentException("El tipo debe ser 'digital' o 'analógico'");
        }
    }
    
    public function setStock(int $stock): void { 
        $this->stock = $stock; 
    }
    
    public function setIdUsuario(?int $id_usuario): void { 
        $this->id_usuario = $id_usuario; 
    }
    
    public function setUrlImagen(string $url_imagen): void { 
        $this->url_imagen = $url_imagen; 
    }
    
    public function setMarca(?string $marca): void {
        $this->marca = $marca;
    }
    
    public function setModelo(?string $modelo): void {
        $this->modelo = $modelo;
    }
    
    public function getNombreCompleto(): string {
        if ($this->marca && $this->modelo) {
            return "{$this->marca} {$this->modelo}";
        }
        
        if ($this->id_marca_modelo > 0) {
            $this->cargarDatosMarcaModelo();
            return "{$this->marca} {$this->modelo}";
        }
        
        return "Reloj #" . ($this->id_reloj ?? 'nuevo');
    }
    
    private function cargarDatosMarcaModelo(): void {
        if (!$this->id_marca_modelo) {
            return;
        }
        
        $conn = Db::getConexion();
        $sql = "SELECT marca, modelo FROM marca_modelo WHERE id_marca_modelo = :id_marca_modelo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_marca_modelo', $this->id_marca_modelo, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $this->marca = $resultado['marca'];
            $this->modelo = $resultado['modelo'];
        }
    }
    
    public function getPrecioFormateado(): string {
        return number_format($this->precio, 2, ',', '.') . ' €';
    }
    
    public function guardar(): bool {
        $conn = Db::getConexion();
        
        $sql = match(true) {
            $this->id_reloj !== null => "UPDATE relojes SET 
                    id_marca_modelo = :id_marca_modelo,
                    precio = :precio,
                    tipo = :tipo,
                    stock = :stock,
                    id_usuario = :id_usuario,
                    url_imagen = :url_imagen
                    WHERE id_reloj = :id_reloj",
            default => "INSERT INTO relojes 
                    (id_marca_modelo, precio, tipo, stock, id_usuario, url_imagen) 
                    VALUES 
                    (:id_marca_modelo, :precio, :tipo, :stock, :id_usuario, :url_imagen)"
        };
        
        $stmt = $conn->prepare($sql);
        
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
            $this->id_reloj = (int)$conn->lastInsertId();
        }
        
        return $resultado;
    }
    
    public function eliminar(): bool {
        if ($this->id_reloj === null) {
            return false;
        }
        
        $conn = Db::getConexion();
        $sql = "DELETE FROM relojes WHERE id_reloj = :id_reloj";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_reloj', $this->id_reloj, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public static function obtenerTodos(): array {
        $conn = Db::getConexion();
        $sql = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                ORDER BY mm.marca, mm.modelo";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $relojes = [];
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultados as $fila) {
            $relojes[] = new Reloj($fila);
        }
        
        return $relojes;
    }
    
    public static function obtenerPorMarca(string $marca): array {
        $conn = Db::getConexion();
        $sql = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                WHERE mm.marca = :marca 
                ORDER BY mm.modelo";
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
    
    public static function obtenerPorId(int $id): ?Reloj {
        $conn = Db::getConexion();
        $sql = "SELECT r.*, mm.marca, mm.modelo 
                FROM relojes r
                JOIN marca_modelo mm ON r.id_marca_modelo = mm.id_marca_modelo
                WHERE r.id_reloj = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $fila ? new Reloj($fila) : null;
    }
    
    public static function obtenerMarcas(): array {
        $conn = Db::getConexion();
        $sql = "SELECT DISTINCT marca FROM marca_modelo ORDER BY marca";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public static function obtenerModelosPorMarca(string $marca): array {
        $conn = Db::getConexion();
        $sql = "SELECT id_marca_modelo, modelo 
                FROM marca_modelo 
                WHERE marca = :marca 
                ORDER BY modelo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function disponible(): bool {
        return $this->stock > 0;
    }
    
    public function reducirStock(int $cantidad = 1): bool {
        if ($this->stock < $cantidad) {
            return false;
        }
        
        $this->stock -= $cantidad;
        return $this->guardar();
    }
    
    public function aumentarStock(int $cantidad = 1): bool {
        $this->stock += $cantidad;
        return $this->guardar();
    }
}