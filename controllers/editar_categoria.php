<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_categoria = (int) $_POST["id_categoria"];
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"] ?? "");

    try {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $stmt = $conexion->prepare("SELECT imagen FROM categoria WHERE id_categoria = ?");
            $stmt->execute([$id_categoria]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row["imagen"])) {
                $oldImage = "../img/categoria/" . $row["imagen"];
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $imagen = uniqid("cat_") . "." . $ext;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../img/categoria/" . $imagen);

            $sql = "UPDATE categoria SET nombre = ?, descripcion = ?, imagen = ? WHERE id_categoria = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $imagen, $id_categoria]);
        } else {
            $sql = "UPDATE categoria SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $id_categoria]);
        }

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=categoria");
    exit();
}
