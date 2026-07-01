<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/estilos.css" type="text/css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-box">
        <h2>Crear nueva cuenta</h2>
        <form action="./controllers/registrar.php" method="POST">
            <div class="input-group">
                <input type="text" name="txt_usuario" placeholder="Nombre de Usuario" required><i class="fa fa-user"></i><br>
            </div>
            <div class="input-group">
                <input type="password" name="txt_pass" placeholder="Contraseña" required><i class="fa fa-lock"></i>
            </div>
            <button type="submit">Registrarse</button>
            <div class="link">
                <p><a href="index.php">¿Ya tienes una cuenta? Inicia Sesion</a></p>
            </div>
        </form>
    </div>
</body>
</html>