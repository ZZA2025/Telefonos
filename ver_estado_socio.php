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

$socio_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $socio_id = $_POST['socio_id'];
    $pagos_realizados = isset($_POST['pagos_realizados']) ? $_POST['pagos_realizados'] : [];

    // Actualizar la tabla pagos_socios
    $stmt_delete = $conn->prepare("DELETE FROM pagos_socios WHERE socio_id = ?");
    $stmt_delete->bind_param("i", $socio_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    foreach ($pagos_realizados as $mes_pago) {
        $metodo_pago = 'Manual';
        $sql_insert_pago = "INSERT INTO pagos_socios (socio_id, mes_pago, metodo_pago) VALUES (?, ?, ?)";
        $stmt_pago = $conn->prepare($sql_insert_pago);
        $stmt_pago->bind_param("iss", $socio_id, $mes_pago, $metodo_pago);
        $stmt_pago->execute();
        $stmt_pago->close();
    }

    // Actualizar el estado_cuenta en la tabla socios
    $estado_cuenta = count($pagos_realizados) > 0 ? 'Pago' : 'Deuda';
    $sql_update = "UPDATE socios SET estado_cuenta=? WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $estado_cuenta, $socio_id);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: ver_estado_socio.php?id=$socio_id");
    exit;
}

$sql = "SELECT s.id, s.nombre, s.apellido, s.dni, ts.tipo AS tipo_socio, ts.monto AS cuota_mensual, s.estado_cuenta, s.fecha_registro,
               GROUP_CONCAT(DISTINCT ps.mes_pago ORDER BY ps.mes_pago ASC) AS pagos_realizados
        FROM socios s
        JOIN tipos_socios ts ON s.tipo_socio_id = ts.id
        LEFT JOIN pagos_socios ps ON s.id = ps.socio_id
        WHERE s.id = ?
        GROUP BY s.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $socio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === FALSE || $result->num_rows == 0) {
    die("Error en la consulta SQL o no se encontró el socio: " . $conn->error);
}

$row = $result->fetch_assoc();
list($total_adeudado, $meses_adeudados) = calcular_deuda($row['fecha_registro'], $row['pagos_realizados'], $row['cuota_mensual']);

$stmt->close();
$conn->close();

function calcular_deuda($fecha_registro, $pagos_realizados, $cuota_mensual) {
    $fecha_actual = new DateTime();
    $inicio = new DateTime($fecha_registro);
    $pagos_realizados = $pagos_realizados ? explode(',', $pagos_realizados) : [];

    $meses_adeudados = [];
    while ($inicio <= $fecha_actual) {
        $mes_string = $inicio->format('Y-m');
        if (!in_array($mes_string, $pagos_realizados)) {
            $meses_adeudados[] = $inicio->format('M');
        }
        $inicio->modify('+1 month');
    }

    $total_adeudado = count($meses_adeudados) * $cuota_mensual;
    return [$total_adeudado, $meses_adeudados];
}

function obtener_todos_los_meses($fecha_registro) {
    $fecha_actual = new DateTime();
    $inicio = new DateTime($fecha_registro);

    $meses = [];
    while ($inicio <= $fecha_actual) {
        $meses[] = $inicio->format('Y-m');
        $inicio->modify('+1 month');
    }

    return $meses;
}

$todos_los_meses = obtener_todos_los_meses($row['fecha_registro']);
?>

<?php include('header.php'); ?>

<div class="main-content">
    <div class="container">
        <h1 class="my-4 text-center">Estado de la Cuenta</h1>
        <form method="POST">
            <div class="form-group">
                <label>Nombre y Apellido:</label>
                <p><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></p>
            </div>
            <div class="form-group">
                <label>DNI:</label>
                <p><?php echo htmlspecialchars($row['dni']); ?></p>
            </div>
            <div class="form-group">
                <label>Tipo de Socio:</label>
                <p><?php echo htmlspecialchars($row['tipo_socio']); ?></p>
            </div>
            <div class="form-group">
                <label>Deuda:</label>
                <p><?php echo number_format($total_adeudado, 2); ?></p>
            </div>
            <div class="form-group">
                <label>Meses Adeudados:</label>
                <p><?php echo htmlspecialchars(implode(', ', $meses_adeudados)); ?></p>
            </div>
            <div class="form-group">
                <label>Seleccione los meses adeudados para marcar como pagados:</label>
                <div class="form-check">
                    <?php foreach ($todos_los_meses as $mes): ?>
                        <input class="form-check-input" type="checkbox" name="pagos_realizados[]" value="<?php echo $mes; ?>"
                            <?php echo in_array($mes, explode(',', $row['pagos_realizados'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><?php echo htmlspecialchars((new DateTime($mes))->format('M Y')); ?></label><br>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" name="socio_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>