<?php

include_once '../conexion/conex.php';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT * FROM proveedor ORDER BY id_proveedor ASC LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM proveedor")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="proveedor-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Proveedor agregado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Proveedor actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Proveedor desactivado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'reactivado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Proveedor activado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="proveedor-header">
        <h3>Listado de Proveedores</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-proveedor">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Razón Social</th>
                    <th>RUC</th>
                    <th>Teléfono</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_proveedor']; ?></td>
                        <td><?php echo htmlspecialchars($row['razon_social']); ?></td>
                        <td><?php echo htmlspecialchars($row['ruc']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefono'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($row['contacto'] ?? '—'); ?></td>
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
                                data-bs-target="#modalEditar<?php echo $row['id_proveedor']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <?php if ($row['activo'] == 1): ?>
                                <a class="btn-eliminar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar<?php echo $row['id_proveedor']; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn-restaurar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReactivar<?php echo $row['id_proveedor']; ?>">
                                    <i class="fa fa-rotate-left"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_proveedor']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Proveedor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_proveedor.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_proveedor"
                                            value="<?php echo $row['id_proveedor']; ?>">
                                        <div class="mb-3">
                                            <label>Razón Social</label>
                                            <input type="text"
                                                name="razon_social"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['razon_social']); ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>RUC</label>
                                            <input type="text"
                                                name="ruc"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['ruc']); ?>"
                                                required
                                                maxlength="11">
                                        </div>
                                        <div class="mb-3">
                                            <label>Teléfono</label>
                                            <input type="text"
                                                name="telefono"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['telefono'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label>Contacto</label>
                                            <input type="text"
                                                name="contacto"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($row['contacto'] ?? ''); ?>">
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
                        id="modalEliminar<?php echo $row['id_proveedor']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea desactivar el proveedor?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['razon_social']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_proveedor.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_proveedor"
                                            value="<?php echo $row['id_proveedor']; ?>">
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
                        id="modalReactivar<?php echo $row['id_proveedor']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Confirmar Reactivación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea volver a activar el proveedor?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['razon_social']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/reactivar_proveedor.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_proveedor"
                                            value="<?php echo $row['id_proveedor']; ?>">
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
                href="principal.php?vista=proveedor&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=proveedor&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=proveedor&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
                <h5 class="modal-title" style="color:black;">Agregar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_proveedor.php" method="POST">
                    <div class="mb-3">
                        <label>Razón Social</label>
                        <input type="text"
                            name="razon_social"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>RUC</label>
                        <input type="text"
                            name="ruc"
                            class="form-control"
                            required
                            maxlength="11">
                    </div>
                    <div class="mb-3">
                        <label>Teléfono</label>
                        <input type="text"
                            name="telefono"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Contacto</label>
                        <input type="text"
                            name="contacto"
                            class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Proveedor
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