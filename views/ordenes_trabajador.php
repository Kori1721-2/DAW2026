<?php

include_once '../conexion/conex.php';

$basePage = 'trabajador';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT o.*, s.nombre AS paquete
        FROM orden_servicio o
        INNER JOIN servicio s ON o.id_servicio = s.id_servicio
        ORDER BY o.id_orden ASC LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$servicios = $conexion->query("SELECT id_servicio, nombre FROM servicio WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM orden_servicio")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<style>
    .orden-servicio-container .table,
    .orden-servicio-container .table td,
    .orden-servicio-container .table th,
    .orden-servicio-container h3,
    .orden-servicio-container label,
    .orden-servicio-container .modal-title,
    .orden-servicio-container th,
    .orden-servicio-container td {
        color: #000 !important;
    }
    .orden-servicio-container .table thead th {
        background: #343a40 !important;
        color: #fff !important;
    }
    .orden-servicio-container .modal-header h5 {
        color: #000 !important;
    }
    .orden-servicio-container .badge {
        color: #fff !important;
    }
    .orden-servicio-container .btn-agregar {
        background: #28a745 !important;
        color: #fff !important;
        padding: 10px 16px !important;
        border-radius: 4px !important;
        text-decoration: none !important;
        font-weight: 600 !important;
    }
    .orden-servicio-container .btn-agregar:hover {
        background: #218838 !important;
        color: #fff !important;
    }
    .orden-servicio-container .btn-editar {
        background: #ffc107 !important;
        color: #000 !important;
    }
    .orden-servicio-container .btn-editar:hover {
        background: #e0a800 !important;
        color: #000 !important;
    }
    .orden-servicio-container .btn-eliminar {
        background: #dc3545 !important;
        color: #fff !important;
    }
    .orden-servicio-container .btn-eliminar:hover {
        background: #bb2d3b !important;
        color: #fff !important;
    }
</style>
<div class="orden-servicio-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Orden agregada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Orden actualizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Orden eliminada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="orden-servicio-header">
        <h3>Listado de Órdenes de Servicio</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
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
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
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
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_orden']; ?>"
                                title="Modificar">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <a class="btn-eliminar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar<?php echo $row['id_orden']; ?>"
                                title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_orden']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Orden</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_orden_servicio.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_orden"
                                            value="<?php echo $row['id_orden']; ?>">
                                        <div class="mb-3">
                                            <label>Paquete</label>
                                            <select name="id_servicio" class="form-control" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($servicios as $s): ?>
                                                    <option value="<?php echo $s['id_servicio']; ?>"
                                                        <?php echo ($s['id_servicio'] == $row['id_servicio']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($s['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Fecha</label>
                                            <input type="date"
                                                name="fecha"
                                                class="form-control"
                                                value="<?php echo $row['fecha']; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Cliente</label>
                                            <input type="text"
                                                name="cliente"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['cliente']); ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Estado</label>
                                            <select name="estado" class="form-control" required>
                                                <option value="pendiente" <?php echo ($row['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="en_curso" <?php echo ($row['estado'] == 'en_curso') ? 'selected' : ''; ?>>En Curso</option>
                                                <option value="completado" <?php echo ($row['estado'] == 'completado') ? 'selected' : ''; ?>>Completado</option>
                                                <option value="cancelado" <?php echo ($row['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Observaciones</label>
                                            <textarea name="observaciones"
                                                class="form-control"
                                                rows="3"><?php echo htmlspecialchars($row['observaciones'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-warning">
                                                Modificar
                                            </button>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                                Cerrar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade"
                        id="modalEliminar<?php echo $row['id_orden']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea eliminar la orden?</p>
                                    <h5 style="color: black;">#<?php echo $row['id_orden']; ?> — <?php echo htmlspecialchars($row['cliente']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_orden_servicio.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_orden"
                                            value="<?php echo $row['id_orden']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            Sí, eliminar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <nav class="pagination is-centered mt-4" role="navigation">
        <?php if ($pagina > 1): ?>
            <a class="pagination-previous"
                style="color:black; text-decoration:none;"
                href="<?php echo $basePage; ?>.php?vista=orden_paquete&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="<?php echo $basePage; ?>.php?vista=orden_paquete&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="<?php echo $basePage; ?>.php?vista=orden_paquete&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<div class="modal fade"
    id="modalAgregar"
    tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" style="color:black;">Agregar Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_orden_servicio.php" method="POST">
                    <div class="mb-3">
                        <label>Paquete</label>
                        <select name="id_servicio" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($servicios as $s): ?>
                                <option value="<?php echo $s['id_servicio']; ?>">
                                    <?php echo htmlspecialchars($s['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date"
                            name="fecha"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Cliente</label>
                        <input type="text"
                            name="cliente"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_curso">En Curso</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea name="observaciones"
                            class="form-control"
                            rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Orden
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
