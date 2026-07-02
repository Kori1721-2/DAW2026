<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../vendor/autoload.php';
require_once '../conexion/conex.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'producto';
$conGrafico = isset($_GET['grafico']) ? $_GET['grafico'] : 'si';

$datos = [];
$columnas = [];
$titulo = '';

switch ($tabla) {
    case 'producto':
        $titulo = 'Reporte de Productos';
        $stmt = $conexion->query("
            SELECT p.id_producto AS codigo, p.nombre, p.unidad_medida, p.precio_unitario,
                   p.stock_minimo, c.nombre AS categoria, pr.razon_social AS proveedor
            FROM producto p
            INNER JOIN categoria c ON p.id_categoria = c.id_categoria
            INNER JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
            ORDER BY p.id_producto
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Unidad Medida', 'Precio Unit.', 'Stock Mín.', 'Categoría', 'Proveedor'];
        break;
    case 'categoria':
        $titulo = 'Reporte de Categorías';
        $stmt = $conexion->query("SELECT id_categoria AS codigo, nombre, descripcion, CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado FROM categoria ORDER BY id_categoria");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Descripción', 'Estado'];
        break;
    case 'proveedor':
        $titulo = 'Reporte de Proveedores';
        $stmt = $conexion->query("SELECT id_proveedor AS codigo, razon_social, ruc, telefono, contacto, CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado FROM proveedor ORDER BY id_proveedor");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Razón Social', 'RUC', 'Teléfono', 'Contacto', 'Estado'];
        break;
    case 'almacen':
        $titulo = 'Reporte de Almacenes';
        $stmt = $conexion->query("SELECT id_almacen AS codigo, nombre, ubicacion, responsable, CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado FROM almacen ORDER BY id_almacen");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Ubicación', 'Responsable', 'Estado'];
        break;
    case 'stock':
        $titulo = 'Reporte de Stock';
        $stmt = $conexion->query("SELECT s.id_stock AS codigo, p.nombre AS producto, a.nombre AS almacen, s.cantidad, s.fecha_actualizacion FROM stock s INNER JOIN producto p ON s.id_producto = p.id_producto INNER JOIN almacen a ON s.id_almacen = a.id_almacen ORDER BY s.id_stock");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Producto', 'Almacén', 'Cantidad', 'Fecha Actualización'];
        break;
    case 'movimiento':
        $titulo = 'Reporte de Movimientos';
        $stmt = $conexion->query("SELECT m.id_movimiento AS codigo, p.nombre AS producto, a.nombre AS almacen, m.tipo, m.cantidad, m.fecha FROM movimiento m INNER JOIN stock s ON m.id_stock = s.id_stock INNER JOIN producto p ON s.id_producto = p.id_producto INNER JOIN almacen a ON s.id_almacen = a.id_almacen ORDER BY m.fecha DESC");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Producto', 'Almacén', 'Tipo', 'Cantidad', 'Fecha'];
        break;
    case 'orden_servicio':
        $titulo = 'Reporte de Órdenes de Servicio';
        $stmt = $conexion->query("SELECT o.id_orden AS codigo, s.nombre AS servicio, o.fecha, o.cliente, o.estado, o.observaciones FROM orden_servicio o INNER JOIN servicio s ON o.id_servicio = s.id_servicio ORDER BY o.fecha DESC");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Servicio', 'Fecha', 'Cliente', 'Estado', 'Observaciones'];
        break;
    case 'servicio':
        $titulo = 'Reporte de Servicios';
        $stmt = $conexion->query("SELECT id_servicio AS codigo, nombre, descripcion, costo_base, CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado FROM servicio ORDER BY id_servicio");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Descripción', 'Costo Base', 'Estado'];
        break;
    default:
        $titulo = 'Reporte de Productos';
        $stmt = $conexion->query("SELECT p.id_producto AS codigo, p.nombre, p.unidad_medida, p.precio_unitario, p.stock_minimo, c.nombre AS categoria, pr.razon_social AS proveedor FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id_categoria INNER JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor ORDER BY p.id_producto");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Unidad Medida', 'Precio Unit.', 'Stock Mín.', 'Categoría', 'Proveedor'];
        break;
}

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; }
    h2 { text-align: center; margin-bottom: 20px; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background-color: #343a40; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td { padding: 4px 8px; border-bottom: 1px solid #dee2e6; }
    tr:nth-child(even) { background-color: #f8f9fa; }
    .footer { text-align: center; font-size: 9px; color: #888; margin-top: 30px; border-top: 1px solid #dee2e6; padding-top: 10px; }
    .fecha { text-align: right; font-size: 10px; color: #666; margin-bottom: 15px; }
</style>
</head>
<body>
    <h2>FUNERARIA SAN PEDRO HUACHO</h2>
    <h3 style="text-align:center;">' . htmlspecialchars($titulo) . '</h3>
    <div class="fecha">Generado: ' . date('d/m/Y H:i') . '</div>
    <table>
        <thead><tr>';

foreach ($columnas as $col) {
    $html .= '<th>' . htmlspecialchars($col) . '</th>';
}

$html .= '</tr></thead><tbody>';

if (empty($datos)) {
    $html .= '<tr><td colspan="' . count($columnas) . '" style="text-align:center;color:#999;">No hay registros</td></tr>';
} else {
    foreach ($datos as $fila) {
        $html .= '<tr>';
        foreach ($fila as $valor) {
            $html .= '<td>' . htmlspecialchars($valor ?? '') . '</td>';
        }
        $html .= '</tr>';
    }
}

$html .= '</tbody></table>';
$html .= '<div class="footer">Sistema de Inventario - Gestión Logística | Desarrollo de Aplicaciones Web</div>';
$html .= '</body></html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream($titulo . '.pdf', ['Attachment' => true]);
exit;
