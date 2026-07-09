<?php
//session_start();
include_once '../conexion/conex.php';

$paquetes = $conexion->query("
    SELECT id_servicio, nombre, descripcion, costo_base, imagen
    FROM servicio WHERE activo = 1
    ORDER BY id_servicio
")->fetchAll(PDO::FETCH_ASSOC);

$paquetesOpciones = $conexion->query("
    SELECT id_servicio, nombre, descripcion, costo_base
    FROM servicio WHERE activo = 1 AND id_servicio IN (1,2,3)
    ORDER BY id_servicio
")->fetchAll(PDO::FETCH_ASSOC);

$servicioTraslado = $conexion->query("SELECT id_servicio, nombre, descripcion, costo_base FROM servicio WHERE id_servicio = 5")->fetch(PDO::FETCH_ASSOC);
$servicioBanda = $conexion->query("SELECT id_servicio, nombre, descripcion, costo_base FROM servicio WHERE id_servicio = 7")->fetch(PDO::FETCH_ASSOC);
$servicioCargadores = $conexion->query("SELECT id_servicio, nombre, descripcion, costo_base FROM servicio WHERE id_servicio = 6")->fetch(PDO::FETCH_ASSOC);

$productos = $conexion->query("
    SELECT p.id_producto, p.nombre, p.unidad_medida, p.precio_unitario, c.nombre AS categoria
    FROM producto p
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria
    ORDER BY c.nombre, p.nombre
")->fetchAll(PDO::FETCH_ASSOC);

$productosPorCategoria = [];
foreach ($productos as $prod) {
    $productosPorCategoria[$prod['categoria']][] = $prod;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Paquetes - Funeraria San Pedro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-paquetes {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        .card-paquete {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-paquete:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .precio-badge {
            background: linear-gradient(135deg, #d4af37, #f7d96b);
        }

        .img-paquete {
            width: 350px;
            height: 340px;
            object-fit: cover;
        }

        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366, #128c7e);
            transition: opacity 0.3s;
        }

        .whatsapp-btn:hover {
            opacity: 0.9;
        }

        .btn-personalizar {
            background: linear-gradient(135deg, #d4af37, #f7d96b);
            color: #1a1a2e;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-personalizar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
        }

        .modal-personalizar .modal-header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #fff;
            border-bottom: 3px solid #d4af37;
        }

        .modal-personalizar .modal-title {
            color: #fff;
            font-weight: bold;
        }

        .modal-personalizar .categoria-item h5 {
            color: #1a1a2e;
            font-weight: 700;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .modal-personalizar .producto-check {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-personalizar .producto-check label {
            flex: 1;
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        .modal-personalizar .producto-check .precio {
            color: #d4af37;
            font-weight: 600;
            font-size: 14px;
        }

        .modal-personalizar .total-estimado {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .modal-personalizar .total-estimado span {
            color: #d4af37;
        }

        .btn-whatsapp-enviar {
            background: linear-gradient(135deg, #25d366, #128c7e);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-whatsapp-enviar:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .modal-personalizar .producto-check {
                flex-wrap: wrap;
            }
        }

        .seccion-personalizar {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            background: #fafafa;
        }

        .seccion-personalizar h5 {
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .seccion-personalizar h5 i {
            color: #d4af37;
            margin-right: 8px;
        }

        .opcion-radio {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .opcion-radio:hover {
            border-color: #d4af37;
            background: #fefce8;
        }

        .opcion-radio input[type="radio"] {
            accent-color: #d4af37;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .opcion-radio label {
            flex: 1;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }

        .opcion-radio .precio-radio {
            color: #d4af37;
            font-weight: 700;
            font-size: 14px;
        }

        .opcion-radio.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f3f4f6;
        }

        .opcion-radio.disabled label,
        .opcion-radio.disabled input {
            cursor: not-allowed;
        }

        .sub-opciones {
            margin-left: 28px;
            padding: 10px 12px;
            background: #fff;
            border-left: 3px solid #d4af37;
            border-radius: 0 8px 8px 0;
            margin-top: 4px;
        }

        .sub-opciones .form-check {
            margin-bottom: 4px;
        }

        .sub-opciones .form-check-label {
            font-size: 13px;
            color: #555;
        }

        .sub-opciones input[type="checkbox"] {
            accent-color: #d4af37;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    <!-- NAVBAR -->
    <nav class="hero-paquetes text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <img src="../img/logo.jpg" alt="Logo" class="h-14 w-14 rounded-full object-cover border-2 border-white">
                    <div>
                        <div class="text-lg font-bold">FUNERARIA SAN PEDRO HUACHO</div>
                        <small class="text-gray-300">Sistema de Inventario - Gestión Logística</small>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn-personalizar" data-bs-toggle="modal" data-bs-target="#modalPersonalizar">
                        <i class="fas fa-sliders-h"></i> Personaliza tu paquete
                    </button>
                    <a href="principal.php?vista=inicio" class="text-white hover:text-amber-300 transition flex items-center gap-1 text-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-paquetes text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">Nuestros Paquetes Funerarios</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Ofrecemos soluciones integrales con calidez y respeto para acompañarte en momentos difíciles.
                Conoce nuestros paquetes diseñados para cada necesidad.
            </p>
        </div>
    </section>

    <!-- PAQUETES -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <?php if (empty($paquetes)): ?>
            <div class="text-center py-20">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl text-gray-500">Próximamente disponibles</h3>
                <p class="text-gray-400">Estamos preparando nuestros paquetes para ti.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($paquetes as $p): ?>
                    <div class="card-paquete bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100">
                        <?php if (!empty($p['imagen'])): ?>
                            <img src="../img/servicio/<?php echo htmlspecialchars($p['imagen']); ?>"
                                alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                                class="w-full img-paquete">
                        <?php else: ?>
                            <div class="w-full img-paquete bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-cross text-6xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($p['descripcion'] ?? 'Sin descripción disponible'); ?>
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="precio-badge text-gray-800 font-bold text-lg px-4 py-2 rounded-lg">
                                    S/. <?php echo number_format($p['costo_base'], 2); ?>
                                </span>
                                <a href="https://wa.me/51954715090?text=Hola,%20quiero%20información%20sobre%20el%20paquete:%20<?php echo urlencode($p['nombre']); ?>"
                                    target="_blank"
                                    class="whatsapp-btn text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium">
                                    <i class="fab fa-whatsapp text-lg"></i> Consultar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- CONTACT INFO -->
    <section class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold mb-6">Contáctanos</h3>
            <div class="flex flex-wrap justify-center gap-8">
                <div class="flex items-center gap-3">
                    <i class="fas fa-phone-alt text-amber-400 text-xl"></i>
                    <span>(01) 239-1206</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fab fa-whatsapp text-amber-400 text-xl"></i>
                    <span>954 715 090</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-amber-400 text-xl"></i>
                    <span>Av. Francisco Vidal 730, Huacho</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm">
            <p>&copy; <?php echo date('Y'); ?> Funeraria San Pedro de Huacho - Todos los derechos reservados</p>
            <p class="mt-1">Desarrollo de Aplicaciones Web - FIISI</p>
        </div>
    </footer>
<div class="modal fade modal-personalizar" id="modalPersonalizar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sliders-h"></i> Personaliza tu Paquete Funerario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <p class="text-muted mb-3">Arma tu paquete paso a paso. Al finalizar envías tu solicitud por WhatsApp.</p>

                <!-- 1. TIPO DE PAQUETE -->
                <div class="seccion-personalizar">
                    <h5><i class="fas fa-star"></i> Tipo de Paquete</h5>
                    <?php foreach ($paquetesOpciones as $po): ?>
                    <div class="opcion-radio">
                        <input type="checkbox" name="tipo_paquete" id="paq_<?php echo $po['id_servicio']; ?>"
                            value="<?php echo $po['id_servicio']; ?>"
                            data-precio="<?php echo $po['costo_base']; ?>"
                            data-nombre="<?php echo htmlspecialchars($po['nombre']); ?>">
                        <label for="paq_<?php echo $po['id_servicio']; ?>">
                            <?php echo htmlspecialchars($po['nombre']); ?>
                            <small class="d-block text-muted" style="font-weight:400;font-size:12px;"><?php echo htmlspecialchars($po['descripcion']); ?></small>
                        </label>
                        <span class="precio-radio">S/. <?php echo number_format($po['costo_base'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- 2. ATAÚD / URNA -->
                <div class="seccion-personalizar">
                    <h5><i class="fas fa-box"></i> Selecciona Ataúd o Urna</h5>
                    <p class="text-muted small mb-2">Si marcas un ataúd, las urnas se deshabilitan y viceversa.</p>
                    <?php
                    $ataudes = $productosPorCategoria['Ataúdes'] ?? [];
                    $urnas = $productosPorCategoria['Urnas'] ?? [];
                    ?>
                    <?php if (!empty($ataudes)): ?>
                        <h6 style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;">⚰️ Ataúdes</h6>
                        <?php foreach ($ataudes as $prod): ?>
                            <div class="opcion-radio ataúd-item">
                                <input type="checkbox" class="radio-ataud"
                                    value="ataud_<?php echo $prod['id_producto']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($prod['nombre'] . ' (' . $prod['unidad_medida'] . ')'); ?>"
                                    data-precio="<?php echo $prod['precio_unitario']; ?>"
                                    data-categoria="ataud">
                                <label><?php echo htmlspecialchars($prod['nombre']); ?></label>
                                <span class="precio-radio">S/. <?php echo number_format($prod['precio_unitario'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($urnas)): ?>
                        <div id="urnasContainer">
                            <h6 style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;margin-top:10px;">🏺 Urnas <small class="text-muted" style="font-weight:400;">(solo disponible con Paquete de Cremación)</small></h6>
                            <?php foreach ($urnas as $prod): ?>
                                <div class="opcion-radio urna-item">
                                    <input type="checkbox" class="radio-urna"
                                        value="urna_<?php echo $prod['id_producto']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($prod['nombre'] . ' (' . $prod['unidad_medida'] . ')'); ?>"
                                        data-precio="<?php echo $prod['precio_unitario']; ?>"
                                        data-categoria="urna">
                                    <label><?php echo htmlspecialchars($prod['nombre']); ?></label>
                                    <span class="precio-radio">S/. <?php echo number_format($prod['precio_unitario'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3. TRASLADO -->
                <div class="seccion-personalizar">
                    <h5><i class="fas fa-truck"></i> Traslado</h5>
                    <div class="opcion-radio">
                        <input type="checkbox" id="chkTraslado"
                            data-precio="<?php echo $servicioTraslado['costo_base']; ?>"
                            data-nombre="<?php echo htmlspecialchars($servicioTraslado['nombre']); ?>">
                        <label for="chkTraslado"><?php echo htmlspecialchars($servicioTraslado['nombre']); ?></label>
                        <span class="precio-radio">S/. <?php echo number_format($servicioTraslado['costo_base'], 2); ?></span>
                    </div>
                </div>

                <!-- 4. PAQUETES ADICIONALES -->
                <div class="seccion-personalizar">
                    <h5><i class="fas fa-plus-circle"></i> Paquetes Adicionales</h5>
                    <div class="opcion-radio">
                        <input type="checkbox" id="chkBanda"
                            data-nombre="<?php echo htmlspecialchars($servicioBanda['nombre']); ?>"
                            data-precio="<?php echo $servicioBanda['costo_base']; ?>">
                        <label for="chkBanda"><?php echo htmlspecialchars($servicioBanda['nombre']); ?></label>
                        <span class="precio-radio">S/. <?php echo number_format($servicioBanda['costo_base'], 2); ?></span>
                    </div>
                    <div class="opcion-radio">
                        <input type="checkbox" id="chkCargadores"
                            data-nombre="<?php echo htmlspecialchars($servicioCargadores['nombre']); ?>"
                            data-precio="<?php echo $servicioCargadores['costo_base']; ?>">
                        <label for="chkCargadores"><?php echo htmlspecialchars($servicioCargadores['nombre']); ?></label>
                        <span class="precio-radio">S/. <?php echo number_format($servicioCargadores['costo_base'], 2); ?></span>
                    </div>
                </div>

                <div class="total-estimado mt-3">
                    Total estimado: S/. <span id="totalPersonalizado">0.00</span>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn-whatsapp-enviar" id="btnEnviarWhatsApp">
                    <i class="fab fa-whatsapp"></i> Enviar solicitud por WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalSpan = document.getElementById('totalPersonalizado');
    const btnEnviar = document.getElementById('btnEnviarWhatsApp');

    const paqueteChecks = document.querySelectorAll('input[name="tipo_paquete"]');
    const chkCremacion = document.getElementById('paq_3');
    const ataudChecks = document.querySelectorAll('.radio-ataud');
    const urnaChecks = document.querySelectorAll('.radio-urna');
    const urnasContainer = document.getElementById('urnasContainer');
    const chkTraslado = document.getElementById('chkTraslado');
    const chkBanda = document.getElementById('chkBanda');
    const chkCargadores = document.getElementById('chkCargadores');

    function actualizarTotal() {
        let total = 0;

        paqueteChecks.forEach(function(cb) {
            if (cb.checked) total += parseFloat(cb.dataset.precio) || 0;
        });

        document.querySelectorAll('.radio-ataud:checked, .radio-urna:checked').forEach(function(cb) {
            total += parseFloat(cb.dataset.precio) || 0;
        });

        if (chkTraslado.checked) {
            total += parseFloat(chkTraslado.dataset.precio) || 0;
        }

        if (chkBanda.checked) total += parseFloat(chkBanda.dataset.precio) || 0;
        if (chkCargadores.checked) total += parseFloat(chkCargadores.dataset.precio) || 0;

        totalSpan.textContent = total.toFixed(2);
    }

    // Mutex: si marcan un paquete, deshabilitar los demás
    function configurarMutexPaquetes() {
        var algunoMarcado = false;
        paqueteChecks.forEach(function(cb) { if (cb.checked) algunoMarcado = true; });

        paqueteChecks.forEach(function(cb) {
            var parent = cb.closest('.opcion-radio');
            if (algunoMarcado && !cb.checked) {
                parent.classList.add('disabled');
                cb.disabled = true;
            } else {
                parent.classList.remove('disabled');
                cb.disabled = false;
            }
        });
    }

    // Urnas solo disponibles si se selecciona Paquete de Cremación
    function configurarUrnasPorCremacion() {
        var cremacionSeleccionada = chkCremacion && chkCremacion.checked;

        urnaChecks.forEach(function(cb) {
            var parent = cb.closest('.opcion-radio');
            if (cremacionSeleccionada) {
                parent.classList.remove('disabled');
                cb.disabled = false;
            } else {
                parent.classList.add('disabled');
                cb.disabled = true;
                cb.checked = false;
            }
        });
    }

    // Mutex: si marcan ataúd, deshabilitar los otros ataúdes y todas las urnas, y viceversa
    function configurarMutexAtaudUrna() {
        const ataudMarcado = document.querySelector('.radio-ataud:checked');
        const urnaMarcada = document.querySelector('.radio-urna:checked');

        // Dentro de ataúdes: solo uno a la vez
        ataudChecks.forEach(function(cb) {
            var parent = cb.closest('.opcion-radio');
            if (ataudMarcado && !cb.checked) {
                parent.classList.add('disabled');
                cb.disabled = true;
            } else {
                parent.classList.remove('disabled');
                cb.disabled = false;
            }
        });

        // Dentro de urnas: solo uno a la vez (no modificar si ninguna está marcada — respeta estado de Cremación)
        if (urnaMarcada) {
            urnaChecks.forEach(function(cb) {
                var parent = cb.closest('.opcion-radio');
                if (!cb.checked) {
                    parent.classList.add('disabled');
                    cb.disabled = true;
                } else {
                    parent.classList.remove('disabled');
                    cb.disabled = false;
                }
            });
        }

        // Cruzado: si hay ataúd marcado, deshabilitar todas las urnas
        if (ataudMarcado) {
            urnaChecks.forEach(function(cb) {
                var parent = cb.closest('.opcion-radio');
                parent.classList.add('disabled');
                cb.disabled = true;
            });
        }

        // Cruzado: si hay urna marcada, deshabilitar todos los ataúdes
        if (urnaMarcada) {
            ataudChecks.forEach(function(cb) {
                var parent = cb.closest('.opcion-radio');
                parent.classList.add('disabled');
                cb.disabled = true;
            });
        }
    }

    // Eventos para actualizar total y mutex
    paqueteChecks.forEach(function(cb) {
        cb.addEventListener('change', function() {
            configurarMutexPaquetes();
            configurarUrnasPorCremacion();
            configurarMutexAtaudUrna();
            actualizarTotal();
        });
    });

    ataudChecks.forEach(function(cb) {
        cb.addEventListener('change', function() {
            configurarMutexAtaudUrna();
            actualizarTotal();
        });
    });

    urnaChecks.forEach(function(cb) {
        cb.addEventListener('change', function() {
            configurarMutexAtaudUrna();
            actualizarTotal();
        });
    });

    chkBanda.addEventListener('change', actualizarTotal);
    chkCargadores.addEventListener('change', actualizarTotal);
    chkTraslado.addEventListener('change', actualizarTotal);

    function enviarWhatsApp() {
        var partes = [];
        var total = 0;

        function agregarSiCheck(check, nombre, precio) {
            if (check.checked) {
                partes.push('• ' + nombre + ' - S/. ' + parseFloat(precio).toFixed(2));
                total += parseFloat(precio) || 0;
            }
        }

        // Tipo de paquete
        paqueteChecks.forEach(function(cb) {
            agregarSiCheck(cb, cb.dataset.nombre, cb.dataset.precio);
        });

        // Ataúd/Urna
        document.querySelectorAll('.radio-ataud:checked, .radio-urna:checked').forEach(function(cb) {
            agregarSiCheck(cb, cb.dataset.nombre, cb.dataset.precio);
        });

        // Traslado
        agregarSiCheck(chkTraslado, chkTraslado.dataset.nombre, chkTraslado.dataset.precio);

        // Adicionales
        agregarSiCheck(chkBanda, chkBanda.dataset.nombre, chkBanda.dataset.precio);
        agregarSiCheck(chkCargadores, chkCargadores.dataset.nombre, chkCargadores.dataset.precio);

        if (partes.length === 0) {
            alert('Selecciona al menos un paquete para personalizar.');
            return;
        }

        var mensaje = 'Hola, quiero personalizar un paquete funerario con lo siguiente:%0A%0A' +
            partes.join('%0A') +
            '%0A%0ATotal estimado: S/. ' + total.toFixed(2) +
            '%0A%0A¿Podrían darme más información?';

        window.open('https://wa.me/51954715090?text=' + mensaje, '_blank');
    }

    btnEnviar.addEventListener('click', enviarWhatsApp);

    // Estado inicial: Urnas deshabilitadas hasta que seleccionen Cremación
    configurarUrnasPorCremacion();
    configurarMutexAtaudUrna();
});
</script>
</body>
</html>