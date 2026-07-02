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

$sql = "SELECT o.*, s.nombre AS paquete
        FROM orden_servicio o
        INNER JOIN servicio s ON o.id_servicio = s.id_servicio
        ORDER BY o.id_orden ASC LIMIT $inicio, $registrosPorPagina";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM orden_servicio")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="container-fluid px-4">
    <h3 class="mb-4"><i class="fas fa-file-signature"></i> Órdenes de Paquetes (Solo Lectura)</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Paquete</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['id_orden']; ?></td>
                    <td><?php echo htmlspecialchars($row['paquete']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                    <td>
                        <?php if ($row['estado'] == 'pendiente'): ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        <?php elseif ($row['estado'] == 'en_curso'): ?>
                            <span class="badge bg-info text-dark">En Curso</span>
                        <?php elseif ($row['estado'] == 'completado'): ?>
                            <span class="badge bg-success">Completado</span>
                        <?php elseif ($row['estado'] == 'cancelado'): ?>
                            <span class="badge bg-danger">Cancelado</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['observaciones'] ?? '—'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination is-centered mt-4" role="navigation">
        <?php if ($pagina > 1): ?>
            <a class="pagination-previous" style="color:black; text-decoration:none;" href="trabajador.php?vista=orden_paquete&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Anterior</a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next" style="color:black; text-decoration:none;" href="trabajador.php?vista=orden_paquete&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">Siguiente</a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li><a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>" style="color:black; text-decoration:none;" href="trabajador.php?vista=orden_paquete&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
