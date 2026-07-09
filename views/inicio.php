<?php

include_once '../conexion/conex.php';

$totalProductos = $conexion->query("SELECT COUNT(*) FROM producto")->fetchColumn();
$totalCategorias = $conexion->query("SELECT COUNT(*) FROM categoria WHERE activo = 1")->fetchColumn();
$totalProveedores = $conexion->query("SELECT COUNT(*) FROM proveedor WHERE activo = 1")->fetchColumn();
$totalAlmacenes = $conexion->query("SELECT COUNT(*) FROM almacen WHERE activo = 1")->fetchColumn();
$totalStock = $conexion->query("SELECT COUNT(*) FROM stock")->fetchColumn();
$totalServicios = $conexion->query("SELECT COUNT(*) FROM servicio WHERE activo = 1")->fetchColumn();
$totalOrdenes = $conexion->query("SELECT COUNT(*) FROM orden_servicio")->fetchColumn();
$totalMovimientos = $conexion->query("SELECT COUNT(*) FROM movimiento")->fetchColumn();

$ordenesEstado = $conexion->query("SELECT estado, COUNT(*) AS total FROM orden_servicio GROUP BY estado")->fetchAll(PDO::FETCH_ASSOC);

$movimientosRecientes = $conexion->query("
    SELECT m.*, p.nombre AS producto, a.nombre AS almacen
    FROM movimiento m
    INNER JOIN stock s ON m.id_stock = s.id_stock
    INNER JOIN producto p ON s.id_producto = p.id_producto
    INNER JOIN almacen a ON s.id_almacen = a.id_almacen
    ORDER BY m.fecha DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$productosStock = $conexion->query("
    SELECT p.id_producto, p.nombre, p.stock_minimo, p.precio_unitario,
           c.nombre AS categoria, pr.razon_social AS proveedor
    FROM producto p
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria
    INNER JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
    ORDER BY p.stock_minimo DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .inicio-container .card,
    .inicio-container .card-body,
    .inicio-container .card-header,
    .inicio-container .card-header h5,
    .inicio-container .table,
    .inicio-container .table td,
    .inicio-container .table th,
    .inicio-container h3,
    .inicio-container h2,
    .inicio-container h5,
    .inicio-container h6 {
        color: #000 !important;
    }
    .inicio-container .text-bg-primary .card-title,
    .inicio-container .text-bg-primary h2,
    .inicio-container .text-bg-success .card-title,
    .inicio-container .text-bg-success h2,
    .inicio-container .text-bg-info .card-title,
    .inicio-container .text-bg-info h2,
    .inicio-container .text-bg-warning .card-title,
    .inicio-container .text-bg-warning h2,
    .inicio-container .text-bg-secondary .card-title,
    .inicio-container .text-bg-secondary h2,
    .inicio-container .text-bg-danger .card-title,
    .inicio-container .text-bg-danger h2 {
        color: #fff !important;
    }
    .inicio-container .card-header {
        background: #343a40 !important;
    }
    .inicio-container .card-header h5 {
        color: #fff !important;
    }
    .inicio-container .table thead th {
        background: #343a40 !important;
        color: #fff !important;
    }
    .inicio-container .badge {
        color: #fff !important;
    }
</style>
<div class="inicio-container">
    <h3 class="mb-4">Dashboard - Panel de Control</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Productos</h6>
                            <h2 class="mb-0"><?php echo $totalProductos; ?></h2>
                        </div>
                        <i class="fa fa-box fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Categorías Activas</h6>
                            <h2 class="mb-0"><?php echo $totalCategorias; ?></h2>
                        </div>
                        <i class="fa fa-tags fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Proveedores</h6>
                            <h2 class="mb-0"><?php echo $totalProveedores; ?></h2>
                        </div>
                        <i class="fa fa-truck fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Almacenes</h6>
                            <h2 class="mb-0"><?php echo $totalAlmacenes; ?></h2>
                        </div>
                        <i class="fa fa-warehouse fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Stock Registrado</h6>
                            <h2 class="mb-0"><?php echo $totalStock; ?></h2>
                        </div>
                        <i class="fa fa-layer-group fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Servicios</h6>
                            <h2 class="mb-0"><?php echo $totalServicios; ?></h2>
                        </div>
                        <i class="fa fa-cross fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Órdenes</h6>
                            <h2 class="mb-0"><?php echo $totalOrdenes; ?></h2>
                        </div>
                        <i class="fa fa-file-signature fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Movimientos</h6>
                            <h2 class="mb-0"><?php echo $totalMovimientos; ?></h2>
                        </div>
                        <i class="fa fa-right-left fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Órdenes por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoOrdenes" height="200"></canvas>
                    <table class="table table-sm mt-3 mb-0">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ordenesEstado as $oe): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($oe['estado']); ?></td>
                                    <td><?php echo $oe['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Productos con Stock Mínimo</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                    <th>Stock Mínimo</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosStock as $ps): ?>
                                    <tr>
                                        <td><?php echo $ps['id_producto']; ?></td>
                                        <td><?php echo htmlspecialchars($ps['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($ps['categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($ps['proveedor']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $ps['stock_minimo'] > 0 ? 'warning' : 'secondary'; ?>">
                                                <?php echo $ps['stock_minimo']; ?>
                                            </span>
                                        </td>
                                        <td>S/. <?php echo number_format($ps['precio_unitario'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Últimos Movimientos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientosRecientes as $m): ?>
                            <tr>
                                <td><?php echo $m['id_movimiento']; ?></td>
                                <td><?php echo htmlspecialchars($m['producto']); ?></td>
                                <td><?php echo htmlspecialchars($m['almacen']); ?></td>
                                <td>
                                    <?php $tipo = $m['tipo']; ?>
                                    <span class="badge bg-<?php echo $tipo === 'entrada' ? 'success' : ($tipo === 'salida' ? 'danger' : 'secondary'); ?>">
                                        <?php echo htmlspecialchars($tipo); ?>
                                    </span>
                                </td>
                                <td><?php echo $m['cantidad']; ?></td>
                                <td><?php echo $m['fecha']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = <?php echo json_encode(array_column($ordenesEstado, 'estado')); ?>;
        const data = <?php echo json_encode(array_column($ordenesEstado, 'total')); ?>;
        const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d', '#0dcaf0'];

        new Chart(document.getElementById('graficoOrdenes'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
