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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $socio_id = $_POST['socio_id'];
    $meses_adeudados = isset($_POST['meses_adeudados']) ? $_POST['meses_adeudados'] : [];
    $monto_socio = $_POST['monto_socio'];
    $monto_actividades = $_POST['monto_actividades'];
    
    $total_deuda = count($meses_adeudados) * ($monto_socio + $monto_actividades);

    // Eliminar los pagos anteriores del socio
    $stmt_delete = $conn->prepare("DELETE FROM pagos_socios WHERE socio_id = ?");
    $stmt_delete->bind_param("i", $socio_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Insertar los nuevos pagos
    foreach ($meses_adeudados as $mes_pago) {
        $metodo_pago = 'Manual';
        $sql_insert_pago = "INSERT INTO pagos_socios (socio_id, mes_pago, metodo_pago) VALUES (?, ?, ?)";
        $stmt_pago = $conn->prepare($sql_insert_pago);
        $stmt_pago->bind_param("iss", $socio_id, $mes_pago, $metodo_pago);
        $stmt_pago->execute();
        $stmt_pago->close();
    }

    // Actualizar el estado de cuenta
    $estado_cuenta = $total_deuda > 0 ? 'Deuda' : 'Al Día';
    $sql_update = "UPDATE socios SET estado_cuenta = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $estado_cuenta, $socio_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirigir a la página pagos.php
    header("Location: pagos.php");
    exit;
}

$conn->close();
?>