<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../views/trabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"] ?? "");

    $imagen = "";
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
        $imagen = uniqid("cat_") . "." . $ext;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/categoria/" . $imagen);
    }

    try {
        if (!empty($imagen)) {
            $sql = "INSERT INTO categoria (nombre, descripcion, imagen, activo) VALUES (?, ?, ?, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $imagen]);
        } else {
            $sql = "INSERT INTO categoria (nombre, descripcion, activo) VALUES (?, ?, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion]);
        }

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=categoria");
    exit();
}
