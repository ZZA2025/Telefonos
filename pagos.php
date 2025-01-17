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

$sql = "SELECT s.id, s.nombre, s.apellido, s.dni, ts.tipo AS tipo_socio, s.estado_cuenta, s.fecha_registro
        FROM socios s
        JOIN tipos_socios ts ON s.tipo_socio_id = ts.id
        WHERE (s.nombre LIKE '%$search_term%' OR s.apellido LIKE '%$search_term%')
        AND (s.dni LIKE '%$search_dni%')";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error en la consulta SQL: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $socio_id = $_POST['socio_id'];
    $metodo_pago = $_POST['metodo_pago'];
    $mes_pago = date('Y-m-d'); // Registrar el mes actual

    $estado_cuenta = ($metodo_pago == 'Efectivo' || $metodo_pago == 'Mercado Pago') ? 'Pago' : 'Deuda';
    
    $sql_update = "UPDATE socios SET estado_cuenta=? WHERE id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $estado_cuenta, $socio_id);
    $stmt->execute();
    $stmt->close();

    // Insertar el pago en la tabla pagos_socios
    if ($estado_cuenta == 'Pago') {
        $sql_insert_pago = "INSERT INTO pagos_socios (socio_id, mes_pago, metodo_pago) VALUES (?, ?, ?)";
        $stmt_pago = $conn->prepare($sql_insert_pago);
        $stmt_pago->bind_param("iss", $socio_id, $mes_pago, $metodo_pago);
        $stmt_pago->execute();
        $stmt_pago->close();
    }
    
    header("Location: pagos.php");
    exit;
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
                        <th>Estado</th>
                        <th>Método de Pago</th>
                        <th>Fecha de registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['dni']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipo_socio']); ?></td>
                                <td class="<?php echo $row['estado_cuenta'] == 'Pago' ? 'status-efectivo' : 'status-no-pago'; ?>">
                                    <?php echo htmlspecialchars($row['estado_cuenta']); ?>
                                </td>
                                <td>
                                    <form method="POST" class="form-inline">
                                        <input type="hidden" name="socio_id" value="<?php echo $row['id']; ?>">
                                        <select name="metodo_pago" class="form-control mb-2 mr-sm-2">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Mercado Pago">Mercado Pago</option>
                                            <option value="No pagó">No pagó</option>
                                        </select>
                                        <button type="submit" class="btn btn-success mb-2">Actualizar</button>
                                    </form>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['fecha_registro']); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay socios registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>