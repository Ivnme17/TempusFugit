<?php
require_once '../Servicio/Db.php';
class Pedidos {
    private $id_pedido;
    private $id_usuario;
    private $id_reloj;
    private $fecha_pedido;
    private $cantidad;
    private $precio_unitario;
    private $precio_total;
    private $metodo_pago;
    private $detalles; 

    public function __construct($id_usuario, $id_reloj, $fecha_pedido, $cantidad = 1, $precio_unitario = 0.00, $metodo_pago = null) {
        $this->id_usuario = $id_usuario;
        $this->id_reloj = $id_reloj;
        $this->fecha_pedido = $fecha_pedido ?? date('Y-m-d H:i:s');
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;       
        $this->metodo_pago = $metodo_pago;
        $this->detalles = []; 
    }

    public function getIdPedido() {
        return $this->id_pedido;
    }
    public function getIdUsuario() {
        return $this->id_usuario;
    }
    public function getIdReloj() {
        return $this->id_reloj;
    }
    public function getFechaPedido() {
        return $this->fecha_pedido;
    }
    public function getCantidad() {
        return $this->cantidad;
    }
    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }
    public function getPrecioTotal() {
        return $this->precio_total;
    }
    public function getMetodoPago() {
        return $this->metodo_pago;
    }
    public function getDetalles() {
        return $this->detalles;
    }
    
    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
    public function setIdReloj($id_reloj) {
        $this->id_reloj = $id_reloj;
    }
    public function setFechaPedido($fecha_pedido) {
        $this->fecha_pedido = $fecha_pedido;
    }
    public function setFechaEntregaEstimada($fecha_entrega_estimada) {
        $this->fecha_entrega_estimada = $fecha_entrega_estimada;
    }
    public function setFechaEntregaReal($fecha_entrega_real) {
        $this->fecha_entrega_real = $fecha_entrega_real;
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }
    public function setPrecioUnitario($precio_unitario) {
        $this->precio_unitario = $precio_unitario;
    }
    public function setPrecioTotal($precio_total) {
        $this->precio_total = $precio_total;
    }
    public function setDireccionEntrega($direccion_entrega) {
        $this->direccion_entrega = $direccion_entrega;
    }
    public function setMetodoPago($metodo_pago) {
        $this->metodo_pago = $metodo_pago;
    }
    public function setCodigoSeguimiento($codigo_seguimiento) {
        $this->codigo_seguimiento = $codigo_seguimiento;
    }
    public function setNotasPedido($notas_pedido) {
        $this->notas_pedido = $notas_pedido;
    }
    public function setCodigoPedido($codigo_Pedido) {
        $this->codigo_Pedido = $codigo_Pedido;
    }
    public function setDetalles($detalles) {
        $this->detalles = $detalles;
    }

    public function insertarPedido() {
        $conexion = Db::getConexion();
        
        try {
            $conexion->beginTransaction();
            
            $consulta = "INSERT INTO pedidos (id_usuario, id_reloj, fecha_pedido, cantidad, precio_unitario, metodo_pago) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([
                $this->id_usuario, 
                $this->id_reloj, 
                $this->fecha_pedido, 
                $this->cantidad, 
                $this->precio_unitario, 
                $this->metodo_pago
            ]);
            
            $this->id_pedido = $conexion->lastInsertId();
            
            if (!empty($this->detalles)) {
                $consultaDetalles = "INSERT INTO detalles_pedido (id_pedido, precio_base, descuento_porcentaje, impuesto_porcentaje, notas) 
                                VALUES (?, ?, ?, ?, ?)";
                $stmtDetalles = $conexion->prepare($consultaDetalles);
                $stmtDetalles->execute([
                    $this->id_pedido,
                    $this->detalles['precio_base'] ?? $this->precio_unitario,
                    $this->detalles['descuento_porcentaje'] ?? 0.00,
                    $this->detalles['impuesto_porcentaje'] ?? 21.00,
                    $this->detalles['notas'] ?? null
                ]);
            }
            
            $conexion->commit();
            return true;
            
        } catch (PDOException $e) {
            $conexion->rollBack();
            error_log("Error al insertar pedido: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPedido() {
        $conexion = Db::getConexion();
        
        try {
            $conexion->beginTransaction();
            
            $consulta = "UPDATE pedidos SET id_usuario=?, id_reloj=?, fecha_pedido=?, cantidad=?, 
                    precio_unitario=?, metodo_pago=? WHERE id_pedido=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([
                $this->id_usuario, 
                $this->id_reloj, 
                $this->fecha_pedido, 
                $this->cantidad, 
                $this->precio_unitario,
                $this->metodo_pago, 
                $this->id_pedido
            ]);
            
            if (!empty($this->detalles)) {
                $obtenerDetalles = "SELECT id_detalle_pedido FROM detalles_pedido WHERE id_pedido = ?";
                $stmt = $conexion->prepare($obtenerDetalles);
                $stmt->execute([$this->id_pedido]);
                
                if ($stmt->rowCount() > 0) {
                    $detalleId = $stmt->fetchColumn();
                    $consultaDetalles = "UPDATE detalles_pedido SET precio_base=?, descuento_porcentaje=?, 
                                    impuesto_porcentaje=?, notas=? WHERE id_detalle_pedido=?";
                    $stmtDetalles = $conexion->prepare($consultaDetalles);
                    $stmtDetalles->execute([
                        $this->detalles['precio_base'] ?? $this->precio_unitario,
                        $this->detalles['descuento_porcentaje'] ?? 0.00,
                        $this->detalles['impuesto_porcentaje'] ?? 21.00,
                        $this->detalles['notas'] ?? null,
                        $detalleId
                    ]);
                } else {
                    $consultaDetalles = "INSERT INTO detalles_pedido (id_pedido, precio_base, descuento_porcentaje, impuesto_porcentaje, notas) 
                                    VALUES (?, ?, ?, ?, ?)";
                    $stmtDetalles = $conexion->prepare($consultaDetalles);
                    $stmtDetalles->execute([
                        $this->id_pedido,
                        $this->detalles['precio_base'] ?? $this->precio_unitario,
                        $this->detalles['descuento_porcentaje'] ?? 0.00,
                        $this->detalles['impuesto_porcentaje'] ?? 21.00,
                        $this->detalles['notas'] ?? null
                    ]);
                }
            }
            
            $conexion->commit();
            return true;
            
        } catch (PDOException $e) {
            $conexion->rollBack();
            error_log("Error al actualizar pedido: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerTodosPedidos() {
        $conexion = Db::getConexion();
        $consulta = "SELECT p.*, dp.precio_base, dp.descuento_porcentaje, dp.impuesto_porcentaje, dp.precio_final, dp.notas 
                FROM pedidos p
                LEFT JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido";
        $stmt = $conexion->query($consulta);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidoPorId($id_pedido) {
        $conexion = Db::getConexion();
        $consulta = "SELECT p.*, dp.precio_base, dp.descuento_porcentaje, dp.impuesto_porcentaje, dp.precio_final, dp.notas 
                FROM pedidos p
                LEFT JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_pedido = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetallesPedido($id_pedido) {
        $conexion = Db::getConexion();
        $consulta = "SELECT * FROM detalles_pedido WHERE id_pedido = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminarPedido() {
        if (!isset($this->id_pedido)) {
            return false;
        }
        
        $conexion = Db::getConexion();
        
        try {
            $conexion->beginTransaction();
            
            $consultaDetalles = "DELETE FROM detalles_pedido WHERE id_pedido = ?";
            $stmtDetalles = $conexion->prepare($consultaDetalles);
            $stmtDetalles->execute([$this->id_pedido]);
            
            $consulta = "DELETE FROM pedidos WHERE id_pedido = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$this->id_pedido]);
            
            $conexion->commit();
            return true;
            
        } catch (PDOException $e) {
            $conexion->rollBack();
            error_log("Error al eliminar pedido: " . $e->getMessage());
            return false;
        }
    }
    public function insertarDetallesPedido($detalles) {
        // Verificar que el pedido ya tenga un ID asignado
        if (!isset($this->id_pedido) || empty($this->id_pedido)) {
            return false;
        }
        
        $conexion = Db::getConexion();
        
        try {
            // Establecer los detalles del pedido en el objeto
            $this->setDetalles($detalles);
            
            // Preparar la consulta para insertar los detalles
            $consultaDetalles = "INSERT INTO detalles_pedido 
                                (id_pedido, id_usuario, precio_base, descuento_porcentaje, 
                                impuesto_porcentaje, fecha_actualizacion, notas) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmtDetalles = $conexion->prepare($consultaDetalles);
            $resultado = $stmtDetalles->execute([
                $this->id_pedido,
                $detalles['id_usuario'] ?? $this->id_usuario,
                $detalles['precio_base'] ?? $this->precio_unitario,
                $detalles['descuento_porcentaje'] ?? 0.00,
                $detalles['impuesto_porcentaje'] ?? 21.00,
                $detalles['fecha_actualizacion'] ?? date('Y-m-d H:i:s'),
                $detalles['notas'] ?? null
            ]);
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error al insertar detalles del pedido: " . $e->getMessage());
            return false;
        }
    }
    public static function obtenerPedidoPorIdUsuario($id_usuario) {
        $conexion = Db::getConexion();
        $consulta = "SELECT p.*, dp.precio_base, dp.descuento_porcentaje, dp.impuesto_porcentaje, dp.precio_final, dp.notas 
                FROM pedidos p
                LEFT JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_usuario = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidosConProductos($id_usuario) {
        $conexion = Db::getConexion();
        $consulta = "SELECT p.*, r.nombre, r.precio 
                FROM pedidos p
                JOIN relojes r ON p.id_reloj = r.id_reloj
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}