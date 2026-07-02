<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['codi_Usuario'])) {
    header("Location: ../index.php");
    exit;
}
include_once '../conexion/conex.php';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT m.*, p.nombre AS producto, a.nombre AS almacen,
               os.id_orden AS orden_ref, os.cliente AS cliente
        FROM movimiento m
        INNER JOIN stock s ON m.id_stock = s.id_stock
        INNER JOIN producto p ON s.id_producto = p.id_producto
        INNER JOIN almacen a ON s.id_almacen = a.id_almacen
        LEFT JOIN orden_servicio os ON m.id_orden = os.id_orden
        ORDER BY m.id_movimiento ASC LIMIT $inicio, $registrosPorPagina";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM movimiento")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="container-fluid px-4">
    <h3 class="mb-4"><i class="fas fa-right-left"></i> Movimientos (Solo Lectura)</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Almacén</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Orden Relacionada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['id_movimiento']; ?></td>
                    <td><?php echo htmlspecialchars($row['producto']); ?></td>
                    <td><?php echo htmlspecialchars($row['almacen']); ?></td>
                    <td>
                        <?php if ($row['tipo'] == 'entrada'): ?>
                            <span class="badge bg-success">Entrada</span>
                        <?php elseif ($row['tipo'] == 'salida'): ?>
                            <span class="badge bg-danger">Salida</span>
                        <?php elseif ($row['tipo'] == 'ajuste'): ?>
                            <span class="badge bg-warning text-dark">Ajuste</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td>
                        <?php if ($row['orden_ref']): ?>
                            #<?php echo $row['orden_ref']; ?> - <?php echo htmlspecialchars($row['cliente'] ?? ''); ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination is-centered mt-4" role="navigation">
        <?php if ($pagina > 1): ?>
            <a class="pagination-previous" style="color:black; text-decoration:none;" href="trabajador.php?vista=movimiento&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Anterior</a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next" style="color:black; text-decoration:none;" href="trabajador.php?vista=movimiento&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Siguiente</a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li><a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>" style="color:black; text-decoration:none;" href="trabajador.php?vista=movimiento&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
