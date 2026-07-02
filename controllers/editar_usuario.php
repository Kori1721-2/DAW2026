<?php
session_start();
include_once "../conexion/conex.php";

include_once '../conexion/conex.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id   = isset($_POST['idusuario']) ? (int) $_POST['idusuario'] : 0;
    $nomb = trim($_POST['nomb_Usuario']);
    $pass = trim($_POST['pass_Usuario']);

    if ($id <= 0 || $nomb === '' || $pass === '') {
        $_SESSION['mensaje'] = 'error_editar';
        header('Location: ../views/principal.php?vista=usuarios');
        exit();
    }

    try {
        // Hashear la nueva contraseña
        $passHash = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios
                SET nomb_Usuario = ?,
                    pass_Usuario = ?
                WHERE idusuario = ?";

        $params = [
            $nomb,
            $passHash,
            $id
        ];

        $stmt = $conexion->prepare($sql);
        $ok = $stmt->execute($params);

        if ($ok) {
            $_SESSION['mensaje'] = 'editado';
        } else {
            $_SESSION['mensaje'] = 'error_editar';
        }

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error_editar';
        // Solo para pruebas:
        // $_SESSION['detalle_error'] = $e->getMessage();
    }

    header('Location: ../views/principal.php?vista=usuarios');
    exit();
}
?>