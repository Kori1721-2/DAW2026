<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once '../conexion/conex.php';

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'producto';
$conGrafico = isset($_GET['grafico']) ? $_GET['grafico'] : 'si';

$datos = [];
$columnas = [];
$titulo = '';
$chartData = null;

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
        if ($conGrafico === 'si') {
            $chartData = $conexion->query("
                SELECT c.nombre AS label, COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                GROUP BY c.nombre
            ")->fetchAll(PDO::FETCH_ASSOC);
        }
        break;

    case 'categoria':
        $titulo = 'Reporte de Categorías';
        $stmt = $conexion->query("
            SELECT id_categoria AS codigo, nombre, descripcion,
                   CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado
            FROM categoria ORDER BY id_categoria
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Descripción', 'Estado'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT CASE WHEN activo=1 THEN 'Activo' ELSE 'Inactivo' END AS label, COUNT(*) AS total FROM categoria GROUP BY activo")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'proveedor':
        $titulo = 'Reporte de Proveedores';
        $stmt = $conexion->query("
            SELECT id_proveedor AS codigo, razon_social, ruc, telefono, contacto,
                   CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado
            FROM proveedor ORDER BY id_proveedor
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Razón Social', 'RUC', 'Teléfono', 'Contacto', 'Estado'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT CASE WHEN activo=1 THEN 'Activo' ELSE 'Inactivo' END AS label, COUNT(*) AS total FROM proveedor GROUP BY activo")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'almacen':
        $titulo = 'Reporte de Almacenes';
        $stmt = $conexion->query("
            SELECT id_almacen AS codigo, nombre, ubicacion, responsable,
                   CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado
            FROM almacen ORDER BY id_almacen
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Ubicación', 'Responsable', 'Estado'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT CASE WHEN activo=1 THEN 'Activo' ELSE 'Inactivo' END AS label, COUNT(*) AS total FROM almacen GROUP BY activo")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'stock':
        $titulo = 'Reporte de Stock';
        $stmt = $conexion->query("
            SELECT s.id_stock AS codigo, p.nombre AS producto, a.nombre AS almacen,
                   s.cantidad, s.fecha_actualizacion
            FROM stock s
            INNER JOIN producto p ON s.id_producto = p.id_producto
            INNER JOIN almacen a ON s.id_almacen = a.id_almacen
            ORDER BY s.id_stock
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Producto', 'Almacén', 'Cantidad', 'Fecha Actualización'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("
                SELECT a.nombre AS label, SUM(s.cantidad) AS total
                FROM stock s INNER JOIN almacen a ON s.id_almacen = a.id_almacen
                GROUP BY a.nombre
            ")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'movimiento':
        $titulo = 'Reporte de Movimientos';
        $stmt = $conexion->query("
            SELECT m.id_movimiento AS codigo, p.nombre AS producto, a.nombre AS almacen,
                   m.tipo, m.cantidad, m.fecha
            FROM movimiento m
            INNER JOIN stock s ON m.id_stock = s.id_stock
            INNER JOIN producto p ON s.id_producto = p.id_producto
            INNER JOIN almacen a ON s.id_almacen = a.id_almacen
            ORDER BY m.fecha DESC
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Producto', 'Almacén', 'Tipo', 'Cantidad', 'Fecha'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT tipo AS label, COUNT(*) AS total FROM movimiento GROUP BY tipo")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'orden_servicio':
        $titulo = 'Reporte de Órdenes de Servicio';
        $stmt = $conexion->query("
            SELECT o.id_orden AS codigo, s.nombre AS servicio, o.fecha, o.cliente,
                   o.estado, o.observaciones
            FROM orden_servicio o
            INNER JOIN servicio s ON o.id_servicio = s.id_servicio
            ORDER BY o.fecha DESC
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Servicio', 'Fecha', 'Cliente', 'Estado', 'Observaciones'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT estado AS label, COUNT(*) AS total FROM orden_servicio GROUP BY estado")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    case 'servicio':
        $titulo = 'Reporte de Servicios';
        $stmt = $conexion->query("
            SELECT id_servicio AS codigo, nombre, descripcion, costo_base,
                   CASE WHEN activo = 1 THEN 'Activo' ELSE 'Inactivo' END AS estado
            FROM servicio ORDER BY id_servicio
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnas = ['Código', 'Nombre', 'Descripción', 'Costo Base', 'Estado'];
        if ($conGrafico === 'si') {
            $r = $conexion->query("SELECT CASE WHEN activo=1 THEN 'Activo' ELSE 'Inactivo' END AS label, COUNT(*) AS total FROM servicio GROUP BY activo")->fetchAll(PDO::FETCH_ASSOC);
            $chartData = $r;
        }
        break;

    default:
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
        if ($conGrafico === 'si') {
            $chartData = $conexion->query("
                SELECT c.nombre AS label, COUNT(*) AS total
                FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                GROUP BY c.nombre
            ")->fetchAll(PDO::FETCH_ASSOC);
        }
        break;
}

// Obtener lista de tablas para el selector
$tablasDisponibles = [
    'producto' => 'Productos',
    'categoria' => 'Categorías',
    'proveedor' => 'Proveedores',
    'almacen' => 'Almacenes',
    'stock' => 'Stock',
    'movimiento' => 'Movimientos',
    'orden_servicio' => 'Órdenes de Servicio',
    'servicio' => 'Servicios'
];
?>
<style>
    .reportes-container .card,
    .reportes-container .card-body,
    .reportes-container .card-header,
    .reportes-container .card-header h5,
    .reportes-container .table,
    .reportes-container .table td,
    .reportes-container .table th,
    .reportes-container h3,
    .reportes-container h2,
    .reportes-container h5,
    .reportes-container h6,
    .reportes-container label,
    .reportes-container .form-select {
        color: #000 !important;
    }
    .reportes-container .card-header {
        background: #343a40 !important;
    }
    .reportes-container .card-header h5 {
        color: #fff !important;
    }
    .reportes-container .table thead th {
        background: #343a40 !important;
        color: #fff !important;
    }
    .reportes-container .badge {
        color: #fff !important;
    }
    .reportes-container .btn-toolbar {
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .reportes-container {
            color: #000 !important;
        }
        .reportes-container .card {
            border: 1px solid #dee2e6 !important;
        }
        .reportes-container .card-header {
            background: #343a40 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .reportes-container .card-header h5 {
            color: #fff !important;
        }
        .reportes-container .table thead th {
            background: #343a40 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .reportes-container .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
<div class="reportes-container">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h3 class="mb-0"><?php echo $titulo; ?></h3>
        <div class="btn-toolbar">
            <a href="../controllers/exportar_pdf.php?tabla=<?php echo urlencode($tabla); ?>&grafico=<?php echo urlencode($conGrafico); ?>" class="btn btn-danger" target="_blank">
                <i class="fa fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="../controllers/exportar_excel.php?tabla=<?php echo urlencode($tabla); ?>&grafico=<?php echo urlencode($conGrafico); ?>" class="btn btn-success">
                <i class="fa fa-file-excel"></i> Exportar Excel
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fa fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="vista" value="reportes">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Seleccionar Reporte</label>
                    <select name="tabla" class="form-select">
                        <?php foreach ($tablasDisponibles as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $tabla === $key ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Incluir Gráfico</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="grafico" value="si" id="grafSi" <?php echo $conGrafico === 'si' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="grafSi">Con gráfico</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="grafico" value="no" id="grafNo" <?php echo $conGrafico === 'no' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="grafNo">Sin gráfico</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($conGrafico === 'si' && !empty($chartData)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Gráfico Estadístico</h5>
        </div>
        <div class="card-body">
            <canvas id="graficoReporte" height="120"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Datos del Reporte</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="tablaReporte">
                    <thead>
                        <tr>
                            <?php foreach ($columnas as $col): ?>
                                <th><?php echo htmlspecialchars($col); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($datos)): ?>
                            <tr>
                                <td colspan="<?php echo count($columnas); ?>" class="text-center text-muted">
                                    No hay registros disponibles
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($datos as $fila): ?>
                                <tr>
                                    <?php foreach ($fila as $key => $valor): ?>
                                        <td><?php echo htmlspecialchars($valor ?? ''); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($conGrafico === 'si' && !empty($chartData)): ?>
        const labels = <?php echo json_encode(array_column($chartData, 'label')); ?>;
        const data = <?php echo json_encode(array_column($chartData, 'total')); ?>;
        const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d', '#0dcaf0'];

        new Chart(document.getElementById('graficoReporte'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad',
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    });
</script>
