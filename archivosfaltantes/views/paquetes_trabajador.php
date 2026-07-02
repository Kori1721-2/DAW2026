<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['codi_Usuario'])) {
    header("Location: ../index.php");
    exit;
}
include_once '../conexion/conex.php';
$stmt = $conexion->query("SELECT * FROM servicio WHERE activo = 1 ORDER BY id_servicio ASC");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid px-4">
    <h3 class="mb-4"><i class="fas fa-cross"></i> Paquetes Funerarios</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Costo Base</th>
                    <th>Imagen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['id_servicio']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion'] ?? '—'); ?></td>
                    <td>S/. <?php echo number_format($row['costo_base'], 2); ?></td>
                    <td>
                        <?php if (!empty($row['imagen'])): ?>
                            <img src="../img/servicio/<?php echo htmlspecialchars($row['imagen']); ?>" style="width:100px;height:60px;object-fit:cover;border-radius:4px;">
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
