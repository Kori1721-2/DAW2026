<?php
//session_start();




if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once '../conexion/conex.php';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT * FROM servicio ORDER BY id_servicio ASC LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM servicio")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="servicio-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Paquete agregado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Paquete actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Paquete desactivado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'reactivado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Paquete activado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="servicio-header">
        <h3>Listado de Paquetes</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-servicio">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Costo Base</th>
                    <th>Imagen</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_servicio']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion'] ?? '—'); ?></td>
                        <td>S/. <?php echo number_format($row['costo_base'], 2); ?></td>
                        <td>
                            <?php if (!empty($row['imagen'])): ?>
                                <img src="../img/servicio/<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen" style="width:250px;height:150px;object-fit:cover;">
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_servicio']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <?php if ($row['activo'] == 1): ?>
                                <a class="btn-eliminar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar<?php echo $row['id_servicio']; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn-restaurar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReactivar<?php echo $row['id_servicio']; ?>">
                                    <i class="fa fa-rotate-left"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_servicio']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Paquete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_servicio.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_servicio"
                                            value="<?php echo $row['id_servicio']; ?>">
                                        <div class="mb-3">
                                            <label>Nombre</label>
                                            <input type="text"
                                                name="nombre"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Descripción</label>
                                            <textarea name="descripcion"
                                                class="form-control"><?php echo htmlspecialchars($row['descripcion'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>Costo Base</label>
                                            <input type="number"
                                                name="costo_base"
                                                class="form-control"
                                                step="0.01"
                                                value="<?php echo $row['costo_base']; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Imagen</label>
                                            <input type="file"
                                                name="imagen"
                                                class="form-control"
                                                accept="image/*">
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
                        id="modalEliminar<?php echo $row['id_servicio']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea desactivar el paquete?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_servicio.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_servicio"
                                            value="<?php echo $row['id_servicio']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            Sí, desactivar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade"
                        id="modalReactivar<?php echo $row['id_servicio']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Confirmar Reactivación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea volver a activar el paquete?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/reactivar_servicio.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_servicio"
                                            value="<?php echo $row['id_servicio']; ?>">
                                        <button type="submit" class="btn btn-primary">
                                            Sí, reactivar
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
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
                href="principal.php?vista=paquete&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=paquete&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=paquete&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
                <h5 class="modal-title" style="color:black;">Agregar Paquete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_servicio.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text"
                            name="nombre"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion"
                            class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Costo Base</label>
                        <input type="number"
                            name="costo_base"
                            class="form-control"
                            step="0.01"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Imagen</label>
                        <input type="file"
                            name="imagen"
                            class="form-control"
                            accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Paquete
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
