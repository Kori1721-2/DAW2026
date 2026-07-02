<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_almacen = (int) $_POST["id_almacen"];
    $nombre = trim($_POST["nombre"]);
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $responsable = trim($_POST["responsable"] ?? "");

    try {
        $sql = "UPDATE almacen SET nombre = ?, ubicacion = ?, responsable = ? WHERE id_almacen = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $ubicacion, $responsable, $id_almacen]);

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=almacen");
    exit();
}
