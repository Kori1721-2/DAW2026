<?php
// Incluir la conexión (Asegúrate de que conex.php use PDO)
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["txt_usuario"]);
    $contrasenia = $_POST["txt_pass"];
    $rol = trim($_POST["txt_rol"] ?? 'Trabajador');

    try {
        //Verificar si el usuario ya existe para evitar duplicados
        $stmt = $conexion->prepare("SELECT idusuario FROM usuarios WHERE nomb_Usuario = :user");
        $stmt->bindParam(':user', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            die("El usuario ya existe.");
        }

        //Encriptar la contraseña
        $passHash = password_hash($contrasenia, PASSWORD_DEFAULT);

        //Insertar en la base de datos
        $insertar = $conexion->prepare("INSERT INTO usuarios (nomb_Usuario, pass_Usuario, estd_Usuario, rol) VALUES (:user, :pass, DEFAULT, :rol)");
        $insertar->bindParam(':user', $usuario);
        $insertar->bindParam(':pass', $passHash);
        $insertar->bindParam(':rol', $rol);

        if ($insertar->execute()) {
            header("Location: ../index.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "Error de registro: " . $e->getMessage();
    }
}
?>
