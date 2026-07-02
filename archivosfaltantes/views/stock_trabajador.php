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

$sql = "SELECT s.id_stock, s.cantidad, s.fecha_actualizacion,
               p.id_producto, p.nombre AS producto, p.unidad_medida,
               a.id_almacen, a.nombre AS almacen
        FROM stock s
        INNER JOIN producto p ON s.id_producto = p.id_producto
        INNER JOIN almacen a ON s.id_almacen = a.id_almacen
        ORDER BY s.id_stock ASC
        LIMIT $inicio, $registrosPorPagina";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM stock")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="container-fluid px-4">
    <h3 class="mb-4"><i class="fas fa-layer-group"></i> Stock (Solo Lectura)</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Unidad Medida</th>
                    <th>Almacén</th>
                    <th>Cantidad</th>
                    <th>Fecha Actualización</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['id_stock']; ?></td>
                    <td><?php echo htmlspecialchars($row['producto']); ?></td>
                    <td><?php echo htmlspecialchars($row['unidad_medida']); ?></td>
                    <td><?php echo htmlspecialchars($row['almacen']); ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo $row['fecha_actualizacion']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination is-centered mt-4" role="navigation">
        <?php if ($pagina > 1): ?>
            <a class="pagination-previous" style="color:black; text-decoration:none;" href="trabajador.php?vista=stock&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Anterior</a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next" style="color:black; text-decoration:none;" href="trabajador.php?vista=stock&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Siguiente</a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li><a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>" style="color:black; text-decoration:none;" href="trabajador.php?vista=stock&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
