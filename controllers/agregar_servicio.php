<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"] ?? "");
    $costo_base = trim($_POST["costo_base"]);

    try {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $nombreImagen = uniqid("ser_") . "." . $extension;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/servicio/" . $nombreImagen);

            $sql = "INSERT INTO servicio (nombre, descripcion, costo_base, imagen, activo) VALUES (?, ?, ?, ?, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $costo_base, $nombreImagen]);
        } else {
            $sql = "INSERT INTO servicio (nombre, descripcion, costo_base, activo) VALUES (?, ?, ?, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $costo_base]);
        }

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=servicio");
    exit();
}
