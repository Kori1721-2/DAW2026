<?php
session_start();
if (!isset($_SESSION['codi_Usuario'])) {
    header("Location: ../index.php");
    exit;
}
$rol = $_SESSION['rol'] ?? '';
$esAdmin = ($rol === 'Administrador');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funeraria San Pedro - Sistema de Inventario</title>

    <!-- Frameworks -->
    <link rel="stylesheet" href="../css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="../css/principal_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/categoria_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/proveedor_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/productos_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/almacen_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/servicio_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/orden_servicio_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/stock_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/movimiento_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/usuario_estilos.css" type="text/css" />
    <link rel="stylesheet" href="../css/animaciones.css" type="text/css" />
</head>

<body>
    <!-- NAVBAR SUPERIOR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="display: flex; justify-content: space-between;">
        <a class="navbar-brand d-flex align-items-center" href="principal.php?vista=inicio">
            <img src="../img/logo.jpg" alt="Logo" style="height: 60px; margin-right: 15px;">
            <div>
                <div style="font-weight: bold;">FUNERARIA SAN PEDRO HUACHO 2026</div>
                <small>Sistema de Inventario - Gestión Logística</small>
            </div>
        </a>
        <a class="btn-cerrar-sesion" href="#" data-bs-toggle="modal" data-bs-target="#modalCerrarSesion"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión</a>
    </nav>
    <!-- SIDEBAR --><?php if ($esAdmin): ?>
    <div class="sidebar">
        <a href="principal.php?vista=inicio"><i class="fas fa-house mr-2"></i> Inicio</a>
        <a href="principal.php?vista=categoria"><i class="fas fa-tags mr-2"></i> Categorías</a>
        <a href="principal.php?vista=proveedor"><i class="fas fa-truck-field mr-2"></i> Proveedores</a>
        <a href="principal.php?vista=stock"><i class="fas fa-layer-group mr-2"></i> Stock</a>
        <a href="principal.php?vista=producto"><i class="fas fa-box-open mr-2"></i> Productos</a>
        <a href="principal.php?vista=almacen"><i class="fas fa-warehouse mr-2"></i> Almacenes</a>
        <a href="principal.php?vista=paquete"><i class="fas fa-cross mr-2"></i> Paquetes</a>
        <a href="principal.php?vista=orden_paquete"><i class="fas fa-file-signature mr-2"></i> Órdenes de Servicio</a>
        <a href="principal.php?vista=movimiento"><i class="fas fa-right-left mr-2"></i> Movimientos</a>
        <a href="principal.php?vista=usuarios"><i class="fas fa-user-tie mr-2"></i> Usuarios</a>
        <a href="principal.php?vista=reportes"><i class="fas fa-chart-column mr-2"></i> Reportes</a>
        <a href="principal.php?vista=configuracion"><i class="fas fa-cogs mr-2"></i> Configuración</a>
        <a href="paquetes_clientes.php" target="_blank" style="border-top: 1px solid #555; margin-top: 5px;">
            <i class="fas fa-star mr-2" style="color: #ffc107;"></i> Ver Paquetes (Clientes)
        </a>
    </div><?php endif; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <?php
        $vista = isset($_GET['vista']) ? $_GET['vista'] : 'inicio';
        switch ($vista) {
            case 'categoria':
                include 'categoria.php';
                break;
            case 'proveedor':
                include 'proveedor.php';
                break;
            case 'producto':
                include 'productos.php';
                break;
            case 'almacen':
                include 'almacen.php';
                break;
            case 'paquete':
                include 'servicio.php';
                break;
            case 'orden_paquete':
                include 'orden_servicio.php';
                break;
            case 'stock':
                include 'stock.php';
                break;
            case 'movimiento':
                include 'movimiento.php';
                break;
            case 'usuarios':
                include 'usuarios.php';
                break;
            case 'reportes':
                include 'reportes.php';
                break;
            case 'configuracion':
                include 'configuracion.php';
                break;
            case 'paquetes_clientes':
                include 'paquetes_clientes.php';
                break;
            default:
                include 'inicio.php';
                break;
        }
        ?>
    </div>
    <!-- FOOTER -->
    <footer>
        <div class="container text-center">
            <h5 class="mb-3">FUNERARIA SAN PEDRO DE HUACHO</h5>
            <p class="mb-1">Sistema de Inventario - Gestión Logística</p>
            <p class="mb-1">Desarrollo de Aplicaciones Web</p>
            <p class="mb-1"><strong>Estudiante:</strong> Corimanya Osorio Luis</p>
            <p class="mb-1"><strong>Ciclo:</strong> IX</p>
            <p class="mb-0"><strong>FIISI</strong></p>
        </div>
    </footer>

    <!-- MODAL CERRAR SESIÓN -->
    <div class="modal fade" id="modalCerrarSesion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar salida</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-sign-out-alt fa-3x text-danger mb-3"></i>
                    <h5>¿Estás seguro de que deseas cerrar sesión?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="../logout.php" class="btn btn-danger">Sí, salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
</body>

</html>
