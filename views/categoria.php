<?php
session_start();

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

$sql = "SELECT * FROM categoria ORDER BY id_categoria ASC LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM categoria")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="categoria-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Categoría agregada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Categoría actualizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Categoría desactivada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'reactivado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Categoría activada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="categoria-header">
        <h3>Listado de Categorías</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-categoria">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_categoria']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion'] ?? '—'); ?></td>
                        <td>
                            <?php if (!empty($row['imagen'])): ?>
                                <img src="../img/categoria/<?php echo htmlspecialchars($row['imagen']); ?>"
                                    alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                                    style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_categoria']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <?php if ($row['activo'] == 1): ?>
                                <a class="btn-eliminar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar<?php echo $row['id_categoria']; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn-restaurar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReactivar<?php echo $row['id_categoria']; ?>">
                                    <i class="fa fa-rotate-left"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_categoria']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Categoría</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_categoria.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_categoria"
                                            value="<?php echo $row['id_categoria']; ?>">
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
                                                class="form-control"
                                                rows="3"><?php echo htmlspecialchars($row['descripcion'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>Imagen</label>
                                            <?php if (!empty($row['imagen'])): ?>
                                                <div class="mb-2">
                                                    <img src="../img/categoria/<?php echo htmlspecialchars($row['imagen']); ?>"
                                                        alt="Imagen actual"
                                                        style="width:80px;height:80px;object-fit:cover;border-radius:4px;">
                                                </div>
                                            <?php endif; ?>
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
                        id="modalEliminar<?php echo $row['id_categoria']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea desactivar la categoría?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_categoria.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_categoria"
                                            value="<?php echo $row['id_categoria']; ?>">
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
                        id="modalReactivar<?php echo $row['id_categoria']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Confirmar Reactivación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea volver a activar la categoría?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/reactivar_categoria.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_categoria"
                                            value="<?php echo $row['id_categoria']; ?>">
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
                href="principal.php?vista=categoria&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=categoria&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=categoria&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
                <h5 class="modal-title" style="color:black;">Agregar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_categoria.php" method="POST" enctype="multipart/form-data">
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
                            class="form-control"
                            rows="3"></textarea>
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
                            Guardar Categoría
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
