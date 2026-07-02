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

$productos = $conexion->query("SELECT id_producto, nombre, unidad_medida FROM producto")->fetchAll(PDO::FETCH_ASSOC);
$almacenes = $conexion->query("SELECT id_almacen, nombre FROM almacen WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM stock")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="stock-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Stock agregado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-primary alert-dismissible fade show" style="color: black;" role="alert">
                Stock actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Stock eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="stock-header">
        <h3>Listado de Stock</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-stock">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Unidad Medida</th>
                    <th>Almacén</th>
                    <th>Cantidad</th>
                    <th>Fecha Actualización</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_stock']; ?></td>
                        <td><?php echo htmlspecialchars($row['producto']); ?></td>
                        <td><?php echo htmlspecialchars($row['unidad_medida']); ?></td>
                        <td><?php echo htmlspecialchars($row['almacen']); ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td><?php echo $row['fecha_actualizacion']; ?></td>
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_stock']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <a class="btn-eliminar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar<?php echo $row['id_stock']; ?>">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_stock']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Stock</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_stock.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_stock"
                                            value="<?php echo $row['id_stock']; ?>">
                                        <div class="mb-3">
                                            <label>Producto</label>
                                            <select name="id_producto" class="form-control" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($productos as $prod): ?>
                                                    <option value="<?php echo $prod['id_producto']; ?>"
                                                        <?php echo ($prod['id_producto'] == $row['id_producto']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($prod['nombre'] . ' (' . $prod['unidad_medida'] . ')'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Almacén</label>
                                            <select name="id_almacen" class="form-control" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($almacenes as $alm): ?>
                                                    <option value="<?php echo $alm['id_almacen']; ?>"
                                                        <?php echo ($alm['id_almacen'] == $row['id_almacen']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($alm['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
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
                                            <label>Fecha de Actualización</label>
                                            <input type="date"
                                                name="fecha_actualizacion"
                                                class="form-control"
                                                value="<?php echo $row['fecha_actualizacion']; ?>"
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
                        id="modalEliminar<?php echo $row['id_stock']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>¿Desea eliminar el stock?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['producto']); ?> — <?php echo htmlspecialchars($row['almacen']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_stock.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_stock"
                                            value="<?php echo $row['id_stock']; ?>">
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
                href="principal.php?vista=stock&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=stock&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=stock&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
                <h5 class="modal-title" style="color:black;">Agregar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_stock.php" method="POST">
                    <div class="mb-3">
                        <label>Producto</label>
                        <select name="id_producto" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($productos as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre'] . ' (' . $prod['unidad_medida'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Almacén</label>
                        <select name="id_almacen" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?php echo $alm['id_almacen']; ?>">
                                    <?php echo htmlspecialchars($alm['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
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
                        <label>Fecha de Actualización</label>
                        <input type="date"
                            name="fecha_actualizacion"
                            class="form-control"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Stock
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
