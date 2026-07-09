<?php

include_once '../conexion/conex.php';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT * FROM usuarios ORDER BY idusuario ASC LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsuarios = $conexion->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

$usuariosActivos = $totalUsuarios;
$usuariosInactivos = 0;


$totalRegistros = $conexion->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="usuarios-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Usuario eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'reactivado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                El usuario se activó correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="usuarios-header">
        <h3>Listado de Usuarios</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['idusuario']; ?></td>
                        <td><?php echo $row['nomb_Usuario']; ?></td>
                        <td><?php echo $row['pass_Usuario']; ?></td>
                        <td>
                            <?php
                            if ($row["estd_Usuario"] == 1) {
                                echo '<span class="badge bg-success">Activo</span>';
                            } else {
                                echo '<span class="badge bg-danger">Inactivo</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['idusuario']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <?php if ($row['estd_Usuario'] == 1): ?>
                                <!-- Botón eliminar -->
                                <a class="btn-eliminar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar<?php echo $row['idusuario']; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <!-- Botón rehabilitar -->
                                <a class="btn-restaurar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReactivar<?php echo $row['idusuario']; ?>">
                                    <i class="fa fa-rotate-left"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['idusuario']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5
                                        class="modal-title"
                                        style="color:black;">
                                        Editar Usuario
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_usuario.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input
                                            type="hidden"
                                            name="idusuario"
                                            value="<?php echo $row['idusuario']; ?>">
                                        <div class="mb-3">
                                            <label>Usuario</label>
                                            <input
                                                type="text"
                                                name="nomb_Usuario"
                                                class="form-control"
                                                value="<?php echo $row['nomb_Usuario']; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Nueva Contraseña</label>
                                            <input
                                                type="password"
                                                name="pass_Usuario"
                                                class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button
                                                type="submit"
                                                class="btn btn-warning">
                                                Modificar
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                data-bs-dismiss="modal">
                                                Cerrar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade"
                        id="modalEliminar<?php echo $row['idusuario']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        Confirmar eliminación
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close btn-close-white"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea eliminar el usuario?</p>
                                    <h5 style="color: black;">
                                        <?php echo $row['nomb_Usuario']; ?>
                                    </h5>
                                </div>
                                <div class="modal-footer">
                                    <form
                                        action="../controllers/eliminar_usuario.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="idusuario"
                                            value="<?php echo $row['idusuario']; ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-danger">
                                            Sí, eliminar
                                        </button>
                                    </form>
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade"
                        id="modalReactivar<?php echo $row['idusuario']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        Confirmar Reactivacion
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close btn-close-white"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea volver a activar el usuario?</p>
                                    <h5 style="color: black;">
                                        <?php echo $row['nomb_Usuario']; ?>
                                    </h5>
                                </div>
                                <div class="modal-footer">
                                    <form
                                        action="../controllers/reactivar_usuario.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="idusuario"
                                            value="<?php echo $row['idusuario']; ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-primary">
                                            Sí, reactivar
                                        </button>
                                    </form>
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        data-bs-dismiss="modal">
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
                href="principal.php?vista=usuarios&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=usuarios&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=usuarios&pagina=<?php echo $i; ?>">
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
                <h5 class="modal-title"
                    style="color:black;">
                    Agregar Usuario
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form
                    action="../controllers/agregar_usuario.php"
                    method="POST">
                    <div class="mb-3">
                        <label>Usuario</label>
                        <input
                            type="text"
                            name="nomb_Usuario"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input
                            type="password"
                            name="pass_Usuario"
                            class="form-control"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="submit"
                            class="btn btn-success">
                            Guardar Usuario
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>