<?php
$db_host = getenv('DB_HOST') . ":3306";
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');

try {
    // Crear la conexion
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Verificar la conexion
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
