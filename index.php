<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistema Web</title>
    <link rel="stylesheet" href="css/estilos.css" type="text/css"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-box">
        <h2>Iniciar Sesion</h2>
        <form action="conexion/validar.php" method="post">
            <div class="input-group">
                <input type="text" name="txtname" placeholder="Usuario" required><i class="fa fa-user"></i><br>
            </div>
            <div class="input-group">
                <input type="password" name="txtpass" placeholder="Contraseña" required><i class="fa fa-lock"></i>
            </div>
            <button type="submit">Acceder</button>
            <div class="link">
                <p><a href="#">¿Olvidaste tu Contraseña?</a></p>
                <p><a href="login_add.php">Crear Cuenta Nueva</a></p>
            </div>
    </form>
    </div>
</body>
</html>