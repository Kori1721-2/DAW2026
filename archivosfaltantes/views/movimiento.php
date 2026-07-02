<?php


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$basePage = ($_SESSION['rol'] === 'Administrador') ? 'principal' : 'trabajador';

include_once '../conexion/conex.php';

$registrosPorPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT m.*, 
               p.nombre AS producto, 
               a.nombre AS almacen,
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

$stockItems = $conexion->query(
    "SELECT s.id_stock, p.nombre AS producto, a.nombre AS almacen 
     FROM stock s 
     INNER JOIN producto p ON s.id_producto = p.id_producto 
     INNER JOIN almacen a ON s.id_almacen = a.id_almacen"
)->fetchAll(PDO::FETCH_ASSOC);

$ordenes = $conexion->query("SELECT id_orden, cliente FROM orden_servicio")->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM movimiento")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="movimiento-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Movimiento agregado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Movimiento actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Movimiento eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="movimiento-header">
        <h3>Listado de Movimientos</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-movimiento">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Almacén</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Orden Relacionada</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
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
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_movimiento']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <a class="btn-eliminar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar<?php echo $row['id_movimiento']; ?>">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_movimiento']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Movimiento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_movimiento.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_movimiento"
                                            value="<?php echo $row['id_movimiento']; ?>">
                                        <div class="mb-3">
                                            <label>Stock (Producto - Almacén)</label>
                                            <select name="id_stock" class="form-control" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($stockItems as $st): ?>
                                                    <option value="<?php echo $st['id_stock']; ?>"
                                                        <?php echo ($st['id_stock'] == $row['id_stock']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($st['producto'] . ' - ' . $st['almacen']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Orden de Servicio (opcional)</label>
                                            <select name="id_orden" class="form-control">
                                                <option value="">Sin orden</option>
                                                <?php foreach ($ordenes as $o): ?>
                                                    <option value="<?php echo $o['id_orden']; ?>"
                                                        <?php echo ($o['id_orden'] == $row['id_orden']) ? 'selected' : ''; ?>>
                                                        #<?php echo $o['id_orden']; ?> - <?php echo htmlspecialchars($o['cliente']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Tipo</label>
                                            <select name="tipo" class="form-control" required>
                                                <option value="entrada" <?php echo ($row['tipo'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                                                <option value="salida" <?php echo ($row['tipo'] == 'salida') ? 'selected' : ''; ?>>Salida</option>
                                                <option value="ajuste" <?php echo ($row['tipo'] == 'ajuste') ? 'selected' : ''; ?>>Ajuste</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Cantidad</label>
                                            <input type="number"
                                                name="cantidad"
                                                class="form-control"
                                                value="<?php echo $row['cantidad']; ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Fecha</label>
                                            <input type="date"
                                                name="fecha"
                                                class="form-control"
                                                value="<?php echo $row['fecha']; ?>"
                                                required>
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
                        id="modalEliminar<?php echo $row['id_movimiento']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea eliminar el movimiento?</p>
                                    <h5 style="color: black;">Movimiento #<?php echo $row['id_movimiento']; ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_movimiento.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_movimiento"
                                            value="<?php echo $row['id_movimiento']; ?>">
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
                href="<?php echo $basePage; ?>.php?vista=movimiento&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="<?php echo $basePage; ?>.php?vista=movimiento&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="<?php echo $basePage; ?>.php?vista=movimiento&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
                <h5 class="modal-title" style="color:black;">Agregar Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_movimiento.php" method="POST">
                    <div class="mb-3">
                        <label>Stock (Producto - Almacén)</label>
                        <select name="id_stock" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($stockItems as $st): ?>
                                <option value="<?php echo $st['id_stock']; ?>">
                                    <?php echo htmlspecialchars($st['producto'] . ' - ' . $st['almacen']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Orden de Servicio (opcional)</label>
                        <select name="id_orden" class="form-control">
                            <option value="">Sin orden</option>
                            <?php foreach ($ordenes as $o): ?>
                                <option value="<?php echo $o['id_orden']; ?>">
                                    #<?php echo $o['id_orden']; ?> - <?php echo htmlspecialchars($o['cliente']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Cantidad</label>
                        <input type="number"
                            name="cantidad"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date"
                            name="fecha"
                            class="form-control"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Movimiento
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
