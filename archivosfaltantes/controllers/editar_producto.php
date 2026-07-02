<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../views/trabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = (int) $_POST["id_producto"];
    $nombre = trim($_POST["nombre"]);
    $unidad_medida = trim($_POST["unidad_medida"]);
    $precio_unitario = (float) $_POST["precio_unitario"];
    $stock_minimo = (int) $_POST["stock_minimo"];
    $id_categoria = (int) $_POST["id_categoria"];
    $id_proveedor = (int) $_POST["id_proveedor"];

    try {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $permitidas = ["jpg", "jpeg", "png", "webp", "gif"];
            if (in_array($ext, $permitidas)) {
                $stmt = $conexion->prepare("SELECT imagen FROM producto WHERE id_producto = ?");
                $stmt->execute([$id_producto]);
                $old = $stmt->fetchColumn();
                if ($old && file_exists("../img/productos/$old")) {
                    unlink("../img/productos/$old");
                }

                $imagen = uniqid("prod_") . "." . $ext;
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/productos/$imagen");

                $sql = "UPDATE producto
                        SET nombre = ?, unidad_medida = ?, precio_unitario = ?,
                            stock_minimo = ?, imagen = ?, id_categoria = ?, id_proveedor = ?
                        WHERE id_producto = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$nombre, $unidad_medida, $precio_unitario, $stock_minimo, $imagen, $id_categoria, $id_proveedor, $id_producto]);
            }
        } else {
            $sql = "UPDATE producto
                    SET nombre = ?, unidad_medida = ?, precio_unitario = ?,
                        stock_minimo = ?, id_categoria = ?, id_proveedor = ?
                    WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $unidad_medida, $precio_unitario, $stock_minimo, $id_categoria, $id_proveedor, $id_producto]);
        }

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=producto");
    exit();
}
