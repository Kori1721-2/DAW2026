<?php
// Credenciales de Clever Cloud
/*$host = "localhost";
$dbname = "fsphuacho";
$user = "root";
$pass = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexion establecida";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}*/
$host     = "bex0i3ljjnuni67tvo73-mysql.services.clever-cloud.com"; 
$dbname   = "bex0i3ljjnuni67tvo73";
$username = "uy981zv6we6civdn";
$password = "ylI0gMyLG1rGMeozUFgd";
$port     = "3306";

try {
    $conexion = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>