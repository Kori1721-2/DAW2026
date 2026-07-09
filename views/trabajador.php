<?php
session_start();
if (!isset($_SESSION['codi_Usuario'])) {
    header("Location: ../index.php");
    exit;
}
$nombreUsuario = $_SESSION['nomb_usuario'] ?? 'Trabajador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funeraria San Pedro - Trabajador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/principal_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/productos_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/stock_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/servicio_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/orden_servicio_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/movimiento_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/usuario_estilos.css" type="text/css">
    <link rel="stylesheet" href="../css/animaciones.css" type="text/css">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand img {
            height: 50px;
            margin-right: 12px;
            border-radius: 8px;
        }
        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }
        .navbar-toggler-icon {
            filter: invert(1);
        }
        .content-worker {
            padding: 90px 25px 40px;
            min-height: 100vh;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: none;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .dashboard-card .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
        }
        .worker-footer {
            background: #1a1a2e;
            color: #aaa;
            text-align: center;
            padding: 18px 0;
            font-size: 14px;
        }
        .navbar {
            height: 56px !important;
        }
        .nav-link-worker {
            color: rgba(255,255,255,0.7) !important;
            padding: 8px 14px !important;
            border-radius: 6px !important;
            transition: all 0.3s !important;
            font-size: 14px !important;
        }
        .nav-link-worker:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.1) !important;
        }
        .navbar-nav .nav-link-worker.active {
            color: #fff !important;
            background: rgba(255,255,255,0.15) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="padding: 0 20px;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="trabajador.php">
                <img src="../img/logo.jpg" alt="Logo" style="height:40px;margin-right:10px;border-radius:6px;">
                <div>
                    <div style="font-weight:bold;font-size:1rem;line-height:1.2;">FUNERARIA SAN PEDRO HUACHO 2026</div>
                    <small>Sistema de Inventario - Acceso Trabajador</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWorker">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarWorker">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php"><i class="fas fa-house"></i> Inicio</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php?vista=stock"><i class="fas fa-layer-group"></i> Stock</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php?vista=producto"><i class="fas fa-box-open"></i> Productos</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php?vista=paquete"><i class="fas fa-cross"></i> Paquetes</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php?vista=orden_paquete"><i class="fas fa-file-signature"></i> Órdenes</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="trabajador.php?vista=movimiento"><i class="fas fa-right-left"></i> Movimientos</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-worker" href="paquetes_clientes.php" target="_blank"><i class="fas fa-star" style="color:#ffc107;"></i> Ver Paquetes</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3" style="font-size:14px;"><i class="fas fa-user"></i> <?php echo htmlspecialchars($nombreUsuario); ?></span>
                    <a class="btn btn-outline-light btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#modalCerrarSesion"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="content-worker">
        <?php
        $vista = $_GET['vista'] ?? 'inicio';
        switch ($vista) {
            case 'stock':
                include 'stock_trabajador.php';
                break;
            case 'producto':
                include 'productos.php';
                break;
            case 'paquete':
                include 'paquetes_trabajador.php';
                break;
            case 'orden_paquete':
                include 'ordenes_trabajador.php';
                break;
            case 'movimiento':
                include 'movimiento_trabajador.php';
                break;
            default:
                ?>
                <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Panel del Trabajador</h2>
                <p class="text-muted mb-4">Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>. Seleccione un módulo del menú superior.</p>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card text-center">
                            <div class="icon-circle bg-warning bg-opacity-10"><i class="fas fa-layer-group" style="color: #ffc107;"></i></div>
                            <h5>Gestión de Stock</h5>
                            <p class="text-muted small">Administre los niveles de inventario y stock de productos.</p>
                            <a href="trabajador.php?vista=stock" class="btn btn-sm btn-outline-warning">Ir a Stock</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card text-center">
                            <div class="icon-circle bg-info bg-opacity-10"><i class="fas fa-right-left" style="color: #0dcaf0;"></i></div>
                            <h5>Movimientos</h5>
                            <p class="text-muted small">Registre entradas, salidas y ajustes de inventario.</p>
                            <a href="trabajador.php?vista=movimiento" class="btn btn-sm btn-outline-info">Ir a Movimientos</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card text-center">
                            <div class="icon-circle bg-success bg-opacity-10"><i class="fas fa-file-signature" style="color: #198754;"></i></div>
                            <h5>Órdenes de Paquetes</h5>
                            <p class="text-muted small">Gestione las órdenes de servicio de paquetes funerarios.</p>
                            <a href="trabajador.php?vista=orden_paquete" class="btn btn-sm btn-outline-success">Ir a Órdenes</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card text-center">
                            <div class="icon-circle bg-primary bg-opacity-10"><i class="fas fa-box-open" style="color: #0d6efd;"></i></div>
                            <h5>Productos</h5>
                            <p class="text-muted small">Consulte el catálogo de productos disponibles.</p>
                            <a href="trabajador.php?vista=producto" class="btn btn-sm btn-outline-primary">Ver Productos</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card text-center">
                            <div class="icon-circle bg-secondary bg-opacity-10"><i class="fas fa-cross" style="color: #6c757d;"></i></div>
                            <h5>Paquetes Funerarios</h5>
                            <p class="text-muted small">Consulte los paquetes funerarios disponibles.</p>
                            <a href="trabajador.php?vista=paquete" class="btn btn-sm btn-outline-secondary">Ver Paquetes</a>
                        </div>
                    </div>
                </div>
                <?php
                break;
        }
        ?>
    </div>

    <footer class="worker-footer">
        <p class="mb-0">Funeraria San Pedro de Huacho — Sistema de Inventario | Acceso Trabajador</p>
    </footer>

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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="../logout.php" class="btn btn-danger">Sí, salir</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
