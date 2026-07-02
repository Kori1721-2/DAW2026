<?php
include 'conexion/conex.php';
try {
    echo "CATEGORIA:\n";
    $q = $conexion->query('DESCRIBE categoria');
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT) . "\n\n";

    echo "PROVEEDOR:\n";
    $q = $conexion->query('DESCRIBE proveedor');
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>