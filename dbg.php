<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
echo "<pre>";

// Autoloader
spl_autoload_register(function($class){
    $p='App\\'; $b=__DIR__.'/app/'; $l=strlen($p);
    if(strncmp($p,$class,$l)!==0) return;
    $f=$b.str_replace('\\','/',substr($class,$l)).'.php';
    if(file_exists($f)) require_once $f;
});

session_start();
echo "SESSION ID: ".session_id()."\n";
echo "cliente_id: ".($_SESSION['cliente_id']??'NO')."\n";
echo "carrito: ".json_encode($_SESSION['carrito']??[])."\n\n";

// Simular checkoutStore con datos reales
if(isset($_SESSION['cliente_id']) && !empty($_SESSION['carrito'])){
    echo "=== SIMULANDO CHECKOUTSTORE ===\n";
    try {
        $conf = require __DIR__.'/config/database.php';
        $dsn="mysql:host={$conf['host']};dbname={$conf['dbname']};charset={$conf['charset']}";
        $db=new PDO($dsn,$conf['username'],$conf['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
        
        $clienteId = $_SESSION['cliente_id'];
        $carrito = $_SESSION['carrito'];
        $subtotal = array_sum(array_column($carrito,'subtotal'));
        $costoEnvio = 5.00;
        $total = $subtotal + $costoEnvio;
        $numPedido = 'PED-DBG-'.time();
        $usuarioSistema = $db->query("SELECT id FROM usuarios WHERE rol='admin' LIMIT 1")->fetchColumn()?:1;
        
        echo "Cliente: $clienteId | Subtotal: $subtotal | Total: $total\n";
        
        $db->beginTransaction();
        $stmt=$db->prepare("INSERT INTO pedidos (numero_pedido,cliente_id,usuario_id,modalidad,estado,subtotal,descuento,costo_envio,total,notas,estado_pago,monto_pagado,saldo_pendiente,direccion_entrega,fecha_pedido) VALUES (?,?,?,?,'pendiente',?,0,?,?,?,'pendiente',0,?,?,NOW())");
        $stmt->execute([$numPedido,$clienteId,$usuarioSistema,'domicilio',$subtotal,$costoEnvio,$total,'debug',$total,'av debug']);
        $pid=$db->lastInsertId();
        echo "INSERT pedido OK - id=$pid\n";
        
        foreach($carrito as $item){
            $db->prepare("INSERT INTO detalle_pedidos (pedido_id,producto_id,cantidad,precio_unitario,subtotal) VALUES(?,?,?,?,?)")
               ->execute([$pid,$item['producto_id'],$item['cantidad'],$item['precio'],$item['subtotal']]);
        }
        echo "INSERT detalles OK\n";
        
        $db->prepare("INSERT INTO historial_estados_pedido (pedido_id,estado_anterior,estado_nuevo,usuario_id,motivo) VALUES (?,NULL,'pendiente',?,'debug')")
           ->execute([$pid,$usuarioSistema]);
        echo "INSERT historial OK\n";
        
        $db->rollBack();
        echo "ROLLBACK OK - TODO FUNCIONA\n";
    } catch(Exception $e){
        if($db->inTransaction()) $db->rollBack();
        echo "ERROR: ".$e->getMessage()."\n";
        echo "FILE: ".$e->getFile()." LINE:".$e->getLine()."\n";
    }
} else {
    echo "No hay sesión de cliente o carrito vacío.\n";
    echo "Para probar: inicia sesión en el portal primero y agrega productos al carrito.\n";
}
echo "</pre>";
