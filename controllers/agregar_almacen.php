<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $ubicacion = trim($_POST["ubicacion"] ?? "");
    $responsable = trim($_POST["responsable"] ?? "");

    try {
        $sql = "INSERT INTO almacen (nombre, ubicacion, responsable, activo) VALUES (?, ?, ?, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $ubicacion, $responsable]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=almacen");
    exit();
}
