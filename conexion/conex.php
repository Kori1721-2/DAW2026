<?php
$host = "localhost";
$dbname = "fsphuacho";
$user = "root";
$pass = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexion establecida";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>