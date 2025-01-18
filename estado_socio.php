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

$sql = "SELECT s.id, s.nombre, s.apellido, s.dni, ts.tipo AS tipo_socio, ts.monto AS monto_socio, s.estado_cuenta,
               GROUP_CONCAT(DISTINCT ps.mes_pago ORDER BY ps.mes_pago ASC) AS pagos_realizados, 
               GROUP_CONCAT(a.nombre SEPARATOR ', ') AS actividades, SUM(a.precio) AS monto_actividades
        FROM socios s
        JOIN tipos_socios ts ON s.tipo_socio_id = ts.id
        LEFT JOIN socios_actividades sa ON s.id = sa.socio_id
        LEFT JOIN actividades a ON sa.actividad_id = a.id
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

$stmt->close();
$conn->close();
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Socio</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .month-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .month-checkbox input {
            margin-right: 5px;
        }
        .debt-label {
            font-weight: bold;
            color: red;
        }
        .card-header, .card-footer {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mt-4">
            <div class="card-header text-center">
                <h2><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></h2>
            </div>
            <div class="card-body">
                <form method="POST" action="actualizar_estado.php">
                    <div class="form-group">
                        <label for="meses">Seleccione los meses adeudados para marcar como pagados:</label>
                        <div id="meses" class="row">
                            <?php
                            $meses = [
                                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                            ];
                            for ($year = 2024; $year <= 2025; $year++) {
                                foreach ($meses as $index => $mes) {
                                    $mes_valor = $year . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                    echo '<div class="col-6 col-sm-4 col-md-3 month-checkbox">';
                                    echo '<input type="checkbox" name="meses_adeudados[]" value="' . $mes_valor . '" id="' . $mes_valor . '">';
                                    echo '<label for="' . $mes_valor . '">' . $mes . ' ' . $year . '</label>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="debt-label">Deuda:</label>
                        <span id="deuda_total"></span>
                    </div>
                    <input type="hidden" name="socio_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="monto_socio" value="<?php echo $row['monto_socio']; ?>">
                    <input type="hidden" name="monto_actividades" value="<?php echo $row['monto_actividades']; ?>">
                    <button type="submit" class="btn btn-primary btn-block">Actualizar Estado</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <small>&copy; 2025 Club de Teléfonos</small>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const montoSocio = <?php echo $row['monto_socio']; ?>;
            const montoActividades = <?php echo $row['monto_actividades']; ?>;
            const checkboxes = document.querySelectorAll('input[name="meses_adeudados[]"]');
            const deudaTotal = document.getElementById('deuda_total');

            function calcularDeuda() {
                let mesesSeleccionados = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        mesesSeleccionados++;
                    }
                });
                const total = mesesSeleccionados * (montoSocio + montoActividades);
                deudaTotal.textContent = total.toFixed(2);
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calcularDeuda);
            });

            calcularDeuda();
        });
    </script>
</body>
</html>

<?php include('footer.php'); ?>