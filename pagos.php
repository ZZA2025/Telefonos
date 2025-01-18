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

$search_term = '';
$search_dni = '';
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
    $search_dni = isset($_GET['search_dni']) ? $_GET['search_dni'] : '';
}

$sql = "SELECT s.id, CONCAT(s.nombre, ' ', s.apellido) AS socio, s.dni, ts.tipo AS tipo_socio, s.estado_cuenta
        FROM socios s
        JOIN tipos_socios ts ON s.tipo_socio_id = ts.id
        WHERE (s.nombre LIKE '%$search_term%' OR s.apellido LIKE '%$search_term%')
        AND (s.dni LIKE '%$search_dni%')";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error en la consulta SQL: " . $conn->error);
}

$conn->close();
?>

<?php include('header.php'); ?>

<div class="main-content">
    <div class="container">
        <h1 class="my-4 text-center">Gestión de Pagos</h1>
        <form method="GET" class="mb-4">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="search_term" class="form-control" placeholder="Nombre o Apellido" value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <input type="text" name="search_dni" class="form-control" placeholder="DNI" value="<?php echo htmlspecialchars($search_dni); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                </div>
            </div>
        </form>
        <form method="GET" class="mb-4">
            <button type="submit" class="btn btn-secondary btn-block">Limpiar Búsqueda</button>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Socio</th>
                        <th>DNI</th>
                        <th>Tipo de Socio</th>
                        <th>Estado de Cuenta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['socio']); ?></td>
                                <td><?php echo htmlspecialchars($row['dni']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipo_socio']); ?></td>
                                <td><?php echo htmlspecialchars($row['estado_cuenta']); ?></td>
                                <td><a href="estado_socio.php?id=<?php echo $row['id']; ?>" class="btn btn-info">Ver Estado</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay socios registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>