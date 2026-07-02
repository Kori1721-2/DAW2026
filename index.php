<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funeraria San Pedro - Sistema de Inventario</title>
    <link rel="stylesheet" href="css/estilos.css" type="text/css"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-header">
                <img src="img/logo.jpg" alt="Logo" class="login-logo">
                <h2>FUNERARIA SAN PEDRO</h2>
                <p class="login-subtitle">Sistema de Inventario - Gestión Logística</p>
            </div>
            <form action="conexion/validar.php" method="post">
                <div class="input-group">
                    <input type="text" name="txtname" placeholder="Usuario" required>
                    <i class="fa fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="txtpass" placeholder="Contraseña" required>
                    <i class="fa fa-lock"></i>
                </div>
                <button type="submit">
                    <i class="fa fa-sign-in-alt mr-2"></i> Ingresar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
