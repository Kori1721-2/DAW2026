<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../vendor/autoload.php';
require_once '../conexion/conex.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'producto';

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle($titulo);

// Header row style
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '343A40']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

// Data style
$dataStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
];

$ultimaColLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columnas));

// Title
$sheet->setCellValue('A1', 'FUNERARIA SAN PEDRO HUACHO');
$sheet->mergeCells('A1:' . $ultimaColLet . '1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', $titulo);
$sheet->mergeCells('A2:' . $ultimaColLet . '2');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'Generado: ' . date('d/m/Y H:i'));
$sheet->mergeCells('A3:' . $ultimaColLet . '3');
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$ultimaCol = count($columnas);
$colLet = 'A';
for ($i = 1; $i <= $ultimaCol; $i++) {
    $colLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $sheet->setCellValue($colLet . 4, $columnas[$i - 1]);
}
$sheet->getStyle('A4:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ultimaCol) . '4')->applyFromArray($headerStyle);

$row = 5;
foreach ($datos as $fila) {
    $i = 1;
    foreach ($fila as $valor) {
        $colLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        $sheet->setCellValue($colLet . $row, $valor ?? '');
        $i++;
    }
    $ultimaColLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($fila));
    $sheet->getStyle('A' . $row . ':' . $ultimaColLet . $row)->applyFromArray($dataStyle);
    $row++;
}

for ($i = 1; $i <= $ultimaCol; $i++) {
    $colLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($colLet)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $titulo . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
