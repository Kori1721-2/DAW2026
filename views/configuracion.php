<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once '../conexion/conex.php';

$dbInfo = $conexion->query("SELECT VERSION() AS version")->fetch(PDO::FETCH_ASSOC);
$tablas = $conexion->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
?>
<style>
    .configuracion-container .card,
    .configuracion-container .card-body,
    .configuracion-container .card-header,
    .configuracion-container .card-header h5,
    .configuracion-container .table,
    .configuracion-container .table td,
    .configuracion-container .table th,
    .configuracion-container h3,
    .configuracion-container h5,
    .configuracion-container h6,
    .configuracion-container .list-group-item {
        color: #000 !important;
    }
    .configuracion-container .card-header {
        background: #343a40 !important;
    }
    .configuracion-container .card-header h5 {
        color: #fff !important;
    }
    .configuracion-container .table thead th {
        background: #343a40 !important;
        color: #fff !important;
    }
    .configuracion-container .list-group-item {
        background: #fff !important;
        border: 1px solid rgba(0,0,0,.125) !important;
    }
    .configuracion-container .list-group-item i {
        color: #6c757d !important;
    }
    .configuracion-container td {
        color: #000 !important;
    }
</style>
<div class="configuracion-container">
    <h3 class="mb-4">Configuración del Sistema</h3>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Información del Sistema</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 180px;">Nombre del Sistema</th>
                                <td>Funeraria San Pedro Huacho 2026</td>
                            </tr>
                            <tr>
                                <th>Versión de MySQL</th>
                                <td><?php echo htmlspecialchars($dbInfo['version']); ?></td>
                            </tr>
                            <tr>
                                <th>Base de Datos</th>
                                <td>fsphuacho</td>
                            </tr>
                        </tbody>
                    </table>
                    <h6 class="mt-3">Tablas del Sistema</h6>
                    <ul class="list-group">
                        <?php foreach ($tablas as $tabla): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa fa-table me-2 text-secondary"></i>
                                <?php echo htmlspecialchars($tabla); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-user me-2"></i>Acerca de</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 180px;">Desarrollador</th>
                                <td>Corimanya Osorio Luis</td>
                            </tr>
                            <tr>
                                <th>Ciclo</th>
                                <td>IX</td>
                            </tr>
                            <tr>
                                <th>Facultad</th>
                                <td>FIISI</td>
                            </tr>
                            <tr>
                                <th>Curso</th>
                                <td>Desarrollo de Aplicaciones Web</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
