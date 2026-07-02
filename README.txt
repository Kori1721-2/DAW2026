HOST: bex0i3ljjnuni67tvo73-mysql.services.clever-cloud.com
DB name: bex0i3ljjnuni67tvo73
USER: uy981zv6we6civdn
PASSWORD: ylI0gMyLG1rGMeozUFgd
PORT: 3306
CONNECTION URI: mysql://uy981zv6we6civdn:ylI0gMyLG1rGMeozUFgd@bex0i3ljjnuni67tvo73-mysql.services.clever-cloud.com:3306/bex0i3ljjnuni67tvo73
MySQL CLI: mysql -h bex0i3ljjnuni67tvo73-mysql.services.clever-cloud.com -P 3306 -u uy981zv6we6civdn -p bex0i3ljjnuni67tvo73

================================================================================
DOCUMENTACION DE CAMBIOS - SISTEMA DE ROLES Y PRIVILEGIOS
================================================================================

A continuacion se detallan todos los cambios implementados en el sistema
Funeraria San Pedro. Estos cambios seran revertidos para que el alumno los
implemente manualmente en clase siguiendo esta guia.

INDICE:
  1.  Login con roles (validar.php)
  2.  Pagina del Trabajador (trabajador.php + subvistas)
  3.  Proteccion de rutas (controladores y vistas)
  4.  Sidebar dinamico (principal.php)
  5.  Registro de usuarios con rol (registrar.php)
  6.  Privilegios CRUD para el Trabajador
  7.  Estilos de la vista del Trabajador
  8.  Guia de implementacion manual paso a paso

================================================================================
1. LOGIN CON ROLES
================================================================================

Archivo: conexion/validar.php

QUE HACE:
  - Al iniciar sesion, la consulta SQL ahora incluye el campo "rol" de la tabla
    "usuarios".
  - Guarda $_SESSION['rol'] con el valor obtenido ('Administrador' o
    'Trabajador').
  - Redirige segun el rol:
      * Administrador -> ../views/principal.php
      * Trabajador    -> ../views/trabajador.php

CAMBIOS EN LA BASE DE DATOS (ejecutar en phpMyAdmin):
  ALTER TABLE usuarios
    ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'Trabajador'
    AFTER pass_Usuario;

  UPDATE usuarios SET rol = 'Administrador' WHERE idusuario = 1;
  -- (el usuario Luis con ID 1 se vuelve Administrador;
  --  los demas quedan como Trabajador)

CODIGO (conexion/validar.php):
  - Cambiar la consulta SELECT para incluir "rol":
      $stmt = $conexion->prepare(
        "SELECT idusuario, pass_Usuario, rol FROM usuarios WHERE nomb_Usuario = :user"
      );
  - Despues de verificar la contrasena, guardar el rol:
      $_SESSION['rol'] = $userRow['rol'];
  - Redirigir segun el rol:
      if ($userRow['rol'] === 'Administrador') {
          header("Location: ../views/principal.php");
      } else {
          header("Location: ../views/trabajador.php");
      }

================================================================================
2. PAGINA DEL TRABAJADOR
================================================================================

Archivo CREADO: views/trabajador.php
Archivos CREADOS: views/stock_trabajador.php
                  views/movimiento_trabajador.php
                  views/paquetes_trabajador.php
                  views/ordenes_trabajador.php
                  views/productos_trabajador.php

QUE HACE:
  - Pagina independiente del panel de administrador.
  - No tiene sidebar lateral, solo un navbar superior (fixed-top).
  - El navbar contiene enlaces a: Inicio, Stock, Movimientos, Paquetes,
    Ordenes, Productos y Salir.
  - El dashboard (vista=inicio_trabajador) muestra:
      * 4 tarjetas KPI con totales (Productos, Stock, Movimientos, Paquetes)
      * 4 tarjetas de acceso directo a cada modulo
  - Cada pestania incluye la vista correspondiente mediante switch.

ESTRUCTURA DEL SWITCH (trabajador.php):
  case 'inicio_trabajador':    -> HTML del dashboard
  case 'stock_trabajador':     -> include 'stock.php';
  case 'movimiento_trabajador':-> include 'movimiento.php';
  case 'paquetes_trabajador':  -> include 'paquetes_trabajador.php';
  case 'ordenes_trabajador':   -> include 'orden_servicio.php';
  case 'productos_trabajador': -> include 'productos_trabajador.php';

VALIDACION AL INICIO:
  <?php
  session_start();
  if (!isset($_SESSION['rol'])) {
      header('Location: ../index.php');
      exit;
  }
  ?>

================================================================================
3. PROTECCION DE RUTAS
================================================================================

3.1 CONTROLADORES (carpeta controllers/)

Archivos modificados (35 archivos):
  agregar_almacen.php, agregar_categoria.php, agregar_movimiento.php,
  agregar_orden_servicio.php, agregar_producto.php, agregar_proveedor.php,
  agregar_servicio.php, agregar_stock.php, agregar_usuario.php,
  editar_almacen.php, editar_categoria.php, editar_movimiento.php,
  editar_orden_servicio.php, editar_producto.php, editar_proveedor.php,
  editar_servicio.php, editar_stock.php, editar_usuario.php,
  eliminar_almacen.php, eliminar_categoria.php, eliminar_movimiento.php,
  eliminar_orden_servicio.php, eliminar_producto.php, eliminar_proveedor.php,
  eliminar_servicio.php, eliminar_stock.php, eliminar_usuario.php,
  exportar_excel.php, exportar_pdf.php, reactivar_almacen.php,
  reactivar_categoria.php, reactivar_proveedor.php, reactivar_servicio.php,
  reactivar_usuario.php, registrar.php

CODIGO AGREGADO al inicio de cada controlador (despues de session_start()):
  if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
      header('Location: ../views/trabajador.php');
      exit;
  }

NOTA: Para los controladores que el Trabajador SI puede usar
(agregar/editar/eliminar de stock, movimiento, orden_servicio y
agregar/editar de producto), la condicion cambia a:
  if (!isset($_SESSION['rol']) ||
      ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Trabajador')) {
      header('Location: ../index.php');
      exit;
  }

3.2 VISTAS ADMIN (carpeta views/)

Archivos modificados (12 archivos):
  almacen.php, categoria.php, configuracion.php, inicio.php, movimiento.php,
  orden_servicio.php, productos.php, proveedor.php, reportes.php,
  servicio.php, stock.php, usuarios.php

CODIGO AGREGADO al inicio (despues de session_start()):
  if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
      header('Location: trabajador.php');
      exit;
  }

NOTA: Las vistas que el Trabajador SI puede ver (stock.php, movimiento.php,
orden_servicio.php) usan una validacion mas permisiva:
  if (!isset($_SESSION['rol']) ||
      ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Trabajador')) {
      header('Location: ../index.php');
      exit;
  }

3.3 PRINCIPAL.PHP

Codigo agregado al inicio del archivo:
  <?php
  session_start();
  if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
      header('Location: trabajador.php');
      exit;
  }
  ?>

================================================================================
4. SIDEBAR DINAMICO
================================================================================

Archivo: views/principal.php

QUE HACE:
  Las opciones del menu lateral (Usuarios, Reportes, Configuracion) se
  muestran solo si el usuario autenticado tiene rol 'Administrador'.

CODIGO:
  <!-- Opciones siempre visibles -->
  <a href="...?vista=inicio">Inicio</a>
  <a href="...?vista=categoria">Categorias</a>
  ... (demas opciones) ...

  <!-- Opciones solo para Administrador -->
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
    <a href="...?vista=usuarios">Usuarios</a>
    <a href="...?vista=reportes">Reportes</a>
    <a href="...?vista=configuracion">Configuracion</a>
  <?php endif; ?>

================================================================================
5. REGISTRO DE USUARIOS CON ROL
================================================================================

Archivo: controllers/registrar.php

QUE HACE:
  - Al crear un nuevo usuario, se inserta el campo "rol" con valor
    'Trabajador' por defecto.
  - Se agrego validacion de sesion para que solo Administradores puedan
    registrar (aunque el formulario esta en usuarios.php que ya esta
    protegido).

INSERT SQL ANTES:
  INSERT INTO usuarios (nomb_Usuario, pass_Usuario, estd_Usuario)
  VALUES (:user, :pass, DEFAULT)

INSERT SQL DESPUES:
  INSERT INTO usuarios (nomb_Usuario, pass_Usuario, rol, estd_Usuario)
  VALUES (:user, :pass, 'Trabajador', DEFAULT)

VALIDACION AGREGADA al inicio:
  session_start();
  if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
      header('Location: ../views/trabajador.php');
      exit;
  }

================================================================================
6. PRIVILEGIOS CRUD PARA EL TRABAJADOR
================================================================================

QUE MODULOS PUEDE USAR EL TRABAJADOR:

  Modulo        | Lectura | Escritura | Vistas usadas
  --------------|---------|-----------|-----------------------
  Stock         | SI      | CRUD      | stock.php
  Movimientos   | SI      | CRUD      | movimiento.php
  Ordenes       | SI      | CRUD      | orden_servicio.php
  Paquetes      | SI      | NO        | paquetes_trabajador.php
  Productos     | SI      | NO        | productos_trabajador.php

6.1 REDIRECCION DINAMICA EN CONTROLADORES

Los controladores que ambos roles pueden usar redirigen segun el rol:

  $destino = $_SESSION['rol'] === 'Administrador'
      ? 'principal.php?vista=movimiento'
      : 'trabajador.php?vista=movimiento_trabajador';
  header("Location: ../views/" . $destino);

Mapeo de vistas por modulo:
  Modulo        | Admin (principal.php) | Worker (trabajador.php)
  --------------|-----------------------|-------------------------
  Movimiento    | vista=movimiento      | vista=movimiento_trabajador
  Stock         | vista=stock           | vista=stock_trabajador
  Orden         | vista=orden_paquete   | vista=ordenes_trabajador
  Producto      | vista=producto        | vista=productos_trabajador

6.2 PAGINACION DINAMICA EN VISTAS COMPARTIDAS

Las vistas stock.php, movimiento.php y orden_servicio.php ahora tienen:

  $vista_actual = $_SESSION['rol'] === 'Administrador'
      ? 'stock' : 'stock_trabajador';
  $base_url = $_SESSION['rol'] === 'Administrador'
      ? 'principal.php' : 'trabajador.php';

Y los enlaces de paginacion usan:
  href="<?php echo $base_url; ?>?vista=<?php echo $vista_actual; ?>&pagina=..."

================================================================================
7. ESTILOS DE LA VISTA DEL TRABAJADOR
================================================================================

Archivo: views/trabajador.php (bloque <style>)

MEJORAS REALIZADAS:
  - Variable CSS personalizadas (--nav-height, --gold, --dark-bg)
  - Navbar con sombra y transiciones suaves
  - Boton hamburguesa (navbar-toggler) para responsive en <992px
  - Menu colapsable con Bootstrap collapse (id="navTrabajador")
  - Fondo oscuro en el menu desplegable mobile
  - Tarjetas KPI con efecto hover (translateY -3px + sombra)
  - Tarjetas de acceso (card-acceso) con hover mas notorio
  - Iconos escalan al hacer hover (scale 1.1)
  - Padding responsive en content-trabajador
  - Clases Bootstrap col-lg-3 en tarjetas de acceso (4 columnas en desktop)

CSS INLINE AGREGADO:
  :root { --nav-height: 64px; --gold: #f7d96b; ... }
  .navbar-trabajador { ... }
  .navbar-toggler { ... }
  @media (max-width: 991px) { ... colapso ... }
  .kpi-trabajador { ... }
  .card-acceso { ... }

================================================================================
8. GUIA DE IMPLEMENTACION MANUAL PASO A PASO
================================================================================

Para implementar todo desde cero, sigue este orden:

PASO 1 - BASE DE DATOS
  Ejecutar en phpMyAdmin:
    ALTER TABLE usuarios
      ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'Trabajador'
      AFTER pass_Usuario;
    UPDATE usuarios SET rol = 'Administrador' WHERE idusuario = 1;

PASO 2 - LOGIN CON ROLES (conexion/validar.php)
  a) Cambiar SELECT: incluir "rol" en la consulta
  b) Guardar $_SESSION['rol'] = $userRow['rol'];
  c) Redirigir segun rol (Administrador -> principal.php,
     Trabajador -> trabajador.php)

PASO 3 - CREAR PAGINA DEL TRABAJADOR
  a) Crear views/trabajador.php con:
     - session_start() y validacion de sesion
     - Navbar superior con Bootstrap
     - Switch de vistas
     - Dashboard con KPIs y tarjetas de acceso
     - Incluir las vistas admin para CRUD (stock.php, movimiento.php,
       orden_servicio.php)
     - Incluir paquetes_trabajador.php (solo lectura)
     - Incluir productos_trabajador.php (solo lectura)
  b) Crear views/paquetes_trabajador.php (read-only)
  c) Crear views/productos_trabajador.php (read-only)

PASO 4 - PROTEGER RUTAS (CONTROLADORES)
  Agregar en cada archivo de controllers/ (despues de session_start()):
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        header('Location: ../views/trabajador.php');
        exit;
    }
  EXCEPCION: los controladores de stock, movimiento, orden_servicio y
  producto (agregar/editar) deben permitir ambos roles:
    if (!isset($_SESSION['rol']) ||
        ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Trabajador')) {
        header('Location: ../index.php');
        exit;
    }
  ADEMAS: agregar redirect dinamico segun rol:
    $destino = $_SESSION['rol'] === 'Administrador'
        ? 'principal.php?vista=...'
        : 'trabajador.php?vista=..._trabajador';
    header("Location: ../views/" . $destino);

PASO 5 - PROTEGER RUTAS (VISTAS ADMIN)
  Agregar en cada vista admin (almacen.php, categoria.php, configuracion.php,
  inicio.php, movimiento.php, orden_servicio.php, productos.php,
  proveedor.php, reportes.php, servicio.php, stock.php, usuarios.php):
  SOLO ADMIN:
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        header('Location: trabajador.php');
        exit;
    }
  AMBOS ROLES (stock.php, movimiento.php, orden_servicio.php):
    if (!isset($_SESSION['rol']) ||
        ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Trabajador')) {
        header('Location: ../index.php');
        exit;
    }
  ADEMAS: en las 3 vistas compartidas, agregar redirect dinamico para
  paginacion:
    $vista_actual = $_SESSION['rol'] === 'Administrador'
        ? 'stock' : 'stock_trabajador';
    $base_url = $_SESSION['rol'] === 'Administrador'
        ? 'principal.php' : 'trabajador.php';
  Y cambiar los href de paginacion de:
    href="principal.php?vista=stock&..."
    a:
    href="<?php echo $base_url; ?>?vista=<?php echo $vista_actual; ?>&..."

PASO 6 - SIDEBAR DINAMICO (views/principal.php)
  a) Agregar al inicio:
       <?php
       session_start();
       if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
           header('Location: trabajador.php');
           exit;
       }
       ?>
  b) Envolver las opciones Usuarios, Reportes, Configuracion en:
       <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
         ... opciones ...
       <?php endif; ?>

PASO 7 - REGISTRO DE USUARIOS (controllers/registrar.php)
  a) Agregar validacion de Administrador al inicio
  b) Cambiar INSERT para incluir "rol":
       INSERT INTO usuarios (nomb_Usuario, pass_Usuario, rol, estd_Usuario)
       VALUES (:user, :pass, 'Trabajador', DEFAULT)

PASO 8 - ESTILOS DEL TRABAJADOR
  Agregar el bloque <style> en trabajador.php con:
     - Variables CSS (--nav-height, --gold, --dark-bg)
     - Estilos para navbar, boton hamburguesa, colapso mobile
     - Estilos para KPIs y tarjetas de acceso
     - Media queries responsive
  El navbar debe usar Bootstrap collapse (data-bs-toggle="collapse",
  data-bs-target="#navTrabajador") para el menu responsive.

PASO 9 - VERIFICACION FINAL
  Probar como Administrador:
     - Login con Luis -> debe ir a principal.php
     - Ver sidebar completo con Usuarios, Reportes, Configuracion
     - Todos los CRUD deben funcionar
  Probar como Trabajador:
     - Login con Pepe -> debe ir a trabajador.php
     - Ver navbar superior con 6 opciones
     - Stock: debe poder agregar, editar, eliminar
     - Movimientos: debe poder agregar, editar, eliminar
     - Ordenes: debe poder agregar, editar, eliminar
     - Paquetes: solo lectura (tarjetas)
     - Productos: solo lectura (tabla)
     - Intentar acceder a principal.php -> debe redirigir a trabajador.php
     - Intentar acceder a controllers admin (ej. agregar_usuario.php)
       -> debe redirigir a trabajador.php
================================================================================