<?php
session_start();
// Incluimos la conexión que ya usa PDO
include_once "./conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["txtname"] ?? '');
    $contrasenia = $_POST["txtpass"] ?? '';

    try {
        $stmt = $conexion->prepare("SELECT idusuario, pass_Usuario FROM usuarios WHERE nomb_Usuario = :user");
        $stmt->bindParam(':user', $usuario);
        $stmt->execute();

        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            if (password_verify($contrasenia, $userRow['pass_Usuario'])) {
                $_SESSION['nomb_usuario'] = $usuario;
                $_SESSION['codi_Usuario'] = $userRow['codi_Usuario'];
                
                header("Location: ../views/principal.php");
                exit;
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "El usuario no existe.";
        }
    } catch (PDOException $e) {
        echo "Error en la validación: " . $e->getMessage();
    }
}
?>
