<?php
//session_start();
include_once '../conexion/conex.php';

$paquetes = $conexion->query("
    SELECT id_servicio, nombre, descripcion, costo_base, imagen
    FROM servicio WHERE activo = 1
    ORDER BY id_servicio
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Paquetes - Funeraria San Pedro</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .precio-badge {
            background: linear-gradient(135deg, #d4af37, #f7d96b);
        }
        .img-paquete {
            height: 220px;
            object-fit: cover;
        }
        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366, #128c7e);
            transition: opacity 0.3s;
        }
        .whatsapp-btn:hover {
            opacity: 0.9;
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
                <div class="flex items-center gap-4">
                    <a href="principal.php?vista=inicio" class="text-white hover:text-amber-300 transition flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Volver al Sistema
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
</body>
</html>
