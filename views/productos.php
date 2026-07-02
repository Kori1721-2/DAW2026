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

$sql = "SELECT p.id_producto, p.nombre, p.unidad_medida, p.precio_unitario,
               p.stock_minimo, p.imagen, p.id_categoria, p.id_proveedor,
               c.nombre AS categoria, pr.razon_social AS proveedor
        FROM producto p
        INNER JOIN categoria c ON p.id_categoria = c.id_categoria
        INNER JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
        ORDER BY p.id_producto ASC
        LIMIT $inicio, $registrosPorPagina";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categorias = $conexion->query("SELECT id_categoria, nombre FROM categoria WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);
$proveedores = $conexion->query("SELECT id_proveedor, razon_social FROM proveedor WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);

$totalRegistros = $conexion->query("SELECT COUNT(*) FROM producto")->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>
<div class="productos-container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php if ($_SESSION['mensaje'] == 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" style="color: black;" role="alert">
                Producto agregado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'editado'): ?>
            <div class="alert alert-warning alert-dismissible fade show" style="color: black;" role="alert">
                Producto actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_SESSION['mensaje'] == 'eliminado'): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="color: black;" role="alert">
                Producto eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="productos-header">
        <h3>Listado de Productos</h3>
        <a class="btn-agregar"
            data-bs-toggle="modal"
            data-bs-target="#modalAgregar">
            <i class="fa fa-plus"></i>
            Agregar
        </a>
    </div>
    <div class="table-responsive">
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Unidad Medida</th>
                    <th>Precio Unitario</th>
                    <th>Stock Mínimo</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row['id_producto']; ?></td>
                        <td>
                            <?php if (!empty($row['imagen'])): ?>
                                <img src="../img/productos/<?php echo $row['imagen']; ?>"
                                    style="width:250px;height:150px;object-fit:cover;border-radius:4px;"
                                    alt="Producto">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['unidad_medida']); ?></td>
                        <td>S/. <?php echo number_format($row['precio_unitario'], 2); ?></td>
                        <td><?php echo $row['stock_minimo']; ?></td>
                        <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($row['proveedor']); ?></td>
                        <td>
                            <a class="btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?php echo $row['id_producto']; ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <br>
                            <a class="btn-eliminar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEliminar<?php echo $row['id_producto']; ?>">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade"
                        id="modalEditar<?php echo $row['id_producto']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" style="color:black;">Editar Producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../controllers/editar_producto.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_producto"
                                            value="<?php echo $row['id_producto']; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label>Producto</label>
                                                    <input type="text"
                                                        name="nombre"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Unidad de Medida</label>
                                                    <input type="text"
                                                        name="unidad_medida"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($row['unidad_medida']); ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Precio Unitario (S/.)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        name="precio_unitario"
                                                        class="form-control"
                                                        value="<?php echo $row['precio_unitario']; ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Stock Mínimo</label>
                                                    <input type="number"
                                                        name="stock_minimo"
                                                        class="form-control"
                                                        value="<?php echo $row['stock_minimo']; ?>"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label>Categoría</label>
                                                    <select name="id_categoria" class="form-control" required>
                                                        <option value="">Seleccione</option>
                                                        <?php foreach ($categorias as $cat): ?>
                                                            <option value="<?php echo $cat['id_categoria']; ?>"
                                                                <?php echo ($cat['id_categoria'] == $row['id_categoria']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($cat['nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Proveedor</label>
                                                    <select name="id_proveedor" class="form-control" required>
                                                        <option value="">Seleccione</option>
                                                        <?php foreach ($proveedores as $prov): ?>
                                                            <option value="<?php echo $prov['id_proveedor']; ?>"
                                                                <?php echo ($prov['id_proveedor'] == $row['id_proveedor']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($prov['razon_social']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Nueva Imagen</label>
                                                    <input type="file"
                                                        name="imagen"
                                                        class="form-control"
                                                        accept="image/*">
                                                </div>
                                                <?php if (!empty($row['imagen'])): ?>
                                                    <div class="text-center">
                                                        <img src="../img/productos/<?php echo $row['imagen']; ?>"
                                                            style="width:120px;height:auto;border-radius:4px;">
                                                        <p class="text-muted small mt-1">Imagen actual</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
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
                        id="modalEliminar<?php echo $row['id_producto']; ?>"
                        tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <?php if (!empty($row['imagen'])): ?>
                                        <img src="../img/productos/<?php echo $row['imagen']; ?>"
                                            style="width:100px;height:100px;object-fit:cover;border-radius:4px;"
                                            class="mb-3">
                                    <?php endif; ?>
                                    <p>¿Desea eliminar el producto?</p>
                                    <h5 style="color: black;"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                </div>
                                <div class="modal-footer">
                                    <form action="../controllers/eliminar_producto.php" method="POST">
                                        <input type="hidden"
                                            name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden"
                                            name="id_producto"
                                            value="<?php echo $row['id_producto']; ?>">
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
                href="principal.php?vista=producto&pagina=<?php echo $pagina - 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina < $totalPaginas): ?>
            <a class="pagination-next"
                style="color:black; text-decoration:none;"
                href="principal.php?vista=producto&pagina=<?php echo $pagina + 1; ?>&limite=<?php echo $registrosPorPagina; ?>">
                Siguiente
            </a>
        <?php endif; ?>
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li>
                    <a class="pagination-link <?php echo ($i == $pagina) ? 'is-current' : ''; ?>"
                        style="color:black; text-decoration: none;"
                        href="principal.php?vista=producto&pagina=<?php echo $i; ?>&limite=<?php echo $registrosPorPagina; ?>">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" style="color:black;">Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/agregar_producto.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden"
                        name="csrf_token"
                        value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Producto</label>
                                <input type="text"
                                    name="nombre"
                                    class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Unidad de Medida</label>
                                <input type="text"
                                    name="unidad_medida"
                                    class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Precio Unitario (S/.)</label>
                                <input type="number"
                                    step="0.01"
                                    name="precio_unitario"
                                    class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Stock Mínimo</label>
                                <input type="number"
                                    name="stock_minimo"
                                    class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Categoría</label>
                                <select name="id_categoria" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>">
                                            <?php echo htmlspecialchars($cat['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Proveedor</label>
                                <select name="id_proveedor" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($proveedores as $prov): ?>
                                        <option value="<?php echo $prov['id_proveedor']; ?>">
                                            <?php echo htmlspecialchars($prov['razon_social']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Imagen</label>
                                <input type="file"
                                    name="imagen"
                                    class="form-control"
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Guardar Producto
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