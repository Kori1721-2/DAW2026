<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_servicio = (int) $_POST["id_servicio"];
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"] ?? "");
    $costo_base = trim($_POST["costo_base"]);

    try {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $stmtImg = $conexion->prepare("SELECT imagen FROM servicio WHERE id_servicio = ?");
            $stmtImg->execute([$id_servicio]);
            $old = $stmtImg->fetch(PDO::FETCH_ASSOC);

            if (!empty($old['imagen']) && file_exists("../img/servicio/" . $old['imagen'])) {
                unlink("../img/servicio/" . $old['imagen']);
            }

            $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $nombreImagen = uniqid("ser_") . "." . $extension;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/servicio/" . $nombreImagen);

            $sql = "UPDATE servicio SET nombre = ?, descripcion = ?, costo_base = ?, imagen = ? WHERE id_servicio = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $costo_base, $nombreImagen, $id_servicio]);
        } else {
            $sql = "UPDATE servicio SET nombre = ?, descripcion = ?, costo_base = ? WHERE id_servicio = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $costo_base, $id_servicio]);
        }

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=paquete");
    exit();
}
