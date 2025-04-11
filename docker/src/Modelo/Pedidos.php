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

    public function __construct($id_usuario, $id_reloj, $fecha_pedido, $cantidad = 1, $precio_unitario = 0.00, $metodo_pago = null) {
        $this->id_usuario = $id_usuario;
        $this->id_reloj = $id_reloj;
        $this->fecha_pedido = $fecha_pedido ?? date('Y-m-d H:i:s');
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;       
        $this->metodo_pago = $metodo_pago;
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

    public function insertarPedido() {
        $conexion = Db::getConexion();
        $sql = "INSERT INTO pedidos (id_usuario, id_reloj, fecha_pedido, cantidad, precio_unitario, metodo_pago) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$this->id_usuario, $this->id_reloj, $this->fecha_pedido, 
                       $this->cantidad, $this->precio_unitario, $this->metodo_pago]);
        return $stmt->rowCount() > 0;
    }

    public function actualizarPedido() {
        $conexion = Db::getConexion();
        $sql = "UPDATE pedidos SET id_usuario=?, id_reloj=?, fecha_pedido=?, cantidad=?, 
                precio_unitario=?, precio_total=?, metodo_pago=? WHERE id_pedido=?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$this->id_usuario, $this->id_reloj, $this->fecha_pedido, 
                       $this->cantidad, $this->precio_unitario, $this->precio_total, 
                       $this->metodo_pago, $this->id_pedido]);
        return $stmt->rowCount() > 0;
    }

    public static function obtenerTodosPedidos() {
        $conexion = Db::getConexion();
        $sql = "SELECT * FROM pedidos";
        $stmt = $conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidoPorId($id_pedido) {
        $conexion = Db::getConexion();
        $sql = "SELECT * FROM pedidos WHERE id_pedido = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


} 