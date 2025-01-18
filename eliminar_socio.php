<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "srv650.hstgr.io";
$username = "u704891060_Hermanos2025";
$password = "Hermanos_2025";
$dbname = "u704891060_club_telefonos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha pasado un ID de socio
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar actividades asociadas al socio
    $sql_actividades = "DELETE FROM socios_actividades WHERE socio_id = ?";
    $stmt_actividades = $conn->prepare($sql_actividades);
    $stmt_actividades->bind_param("i", $id);
    if (!$stmt_actividades->execute()) {
        die("Error al eliminar actividades del socio: " . $stmt_actividades->error);
    }
    $stmt_actividades->close();

    // Eliminar el socio
    $sql_socio = "DELETE FROM socios WHERE id = ?";
    $stmt_socio = $conn->prepare($sql_socio);
    $stmt_socio->bind_param("i", $id);
    if ($stmt_socio->execute()) {
        header("Location: ver_socios.php");
        exit;
    } else {
        die("Error al eliminar el socio: " . $stmt_socio->error);
    }
    $stmt_socio->close();
} else {
    die("ID de socio no especificado.");
}

$conn->close();
?>