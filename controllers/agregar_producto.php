<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $unidad_medida = trim($_POST["unidad_medida"]);
    $precio_unitario = (float) $_POST["precio_unitario"];
    $stock_minimo = (int) $_POST["stock_minimo"];
    $id_categoria = (int) $_POST["id_categoria"];
    $id_proveedor = (int) $_POST["id_proveedor"];
    $imagen = null;

    try {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $permitidas = ["jpg", "jpeg", "png", "webp", "gif"];
            if (in_array($ext, $permitidas)) {
                $imagen = uniqid("prod_") . "." . $ext;
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/productos/$imagen");
            }
        }

        $sql = "INSERT INTO producto (nombre, unidad_medida, precio_unitario, stock_minimo, imagen, id_categoria, id_proveedor)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $unidad_medida, $precio_unitario, $stock_minimo, $imagen, $id_categoria, $id_proveedor]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=producto");
    exit();
}

