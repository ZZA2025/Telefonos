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

// Consulta para contar el número de socios
$sql_socios = "SELECT COUNT(*) as total_socios FROM socios";
$result_socios = $conn->query($sql_socios);

if ($result_socios === FALSE) {
    die("Error en la consulta SQL: " . $conn->error);
}

$row_socios = $result_socios->fetch_assoc();
$total_socios = $row_socios['total_socios'];

$conn->close();
?>

<?php include('header.php'); ?>

<div class="main-content">
    <div class="container">
        <h1 class="my-4 text-center">Panel de Gestión</h1>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-white bg-primary text-center">
                    <div class="card-body">
                        <h5 class="card-title">Cantidad de Socios</h5>
                        <p class="card-text display-4" id="cantidad_socios"><?php echo $total_socios; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-white bg-secondary text-center">
                    <div class="card-body">
                        <h5 class="card-title">Alquileres de Quinchos</h5>
                        <p class="card-text display-4" id="cantidad_quinchos">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-white bg-success text-center">
                    <div class="card-body">
                        <h5 class="card-title">Estacionamientos</h5>
                        <p class="card-text display-4" id="cantidad_estacionamientos">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-white bg-danger text-center">
                    <div class="card-body">
                        <h5 class="card-title">Colonia de Vacaciones</h5>
                        <p class="card-text display-4" id="cantidad_colonia">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>