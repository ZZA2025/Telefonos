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

// Consulta SQL con LEFT JOIN para obtener socios, sus actividades y calcular la cuota
$sql = "SELECT s.id, s.nombre, s.apellido, ts.tipo AS tipo_socio, ts.monto AS valor_tipo_socio, s.estado_cuenta,
               GROUP_CONCAT(a.nombre SEPARATOR ', ') AS actividades,
               SUM(a.precio) + ts.monto AS cuota
        FROM socios s
        JOIN tipos_socios ts ON s.tipo_socio_id = ts.id
        LEFT JOIN socios_actividades sa ON s.id = sa.socio_id
        LEFT JOIN actividades a ON sa.actividad_id = a.id
        GROUP BY s.id";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error en la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Socios</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .content {
            flex: 1 0 auto;
        }
        .sidebar {
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .status-efectivo {
            color: green;
        }
        .status-mercado-pago {
            color: skyblue;
        }
        .status-no-pago {
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center">Gestión de Socios</h3>
        <a href="panel.php">Panel</a>
        <a href="#gestionar" data-toggle="collapse">Gestionar</a>
        <div id="gestionar" class="collapse">
            <a href="agregar_socio.php" class="pl-3">Agregar Socio</a>
            <a href="#" class="pl-3">Alquiler de Quinchos</a>
            <a href="#" class="pl-3">Estacionamientos</a>
            <a href="#" class="pl-3">Colonia de Vacaciones</a>
        </div>
        <a href="#visualizar" data-toggle="collapse">Visualizar</a>
        <div id="visualizar" class="collapse">
            <a href="ver_socios.php" class="pl-3">Ver Socios</a>
            <a href="#" class="pl-3">Quinchos Alquilados</a>
            <a href="#" class="pl-3">Estacionamientos</a>
            <a href="#" class="pl-3">Colonia de Vacaciones</a>
        </div>
        <a href="logout.php" class="mt-5 btn btn-danger btn-block">Cerrar Sesión</a>
    </div>

    <div class="main-content">
        <div class="container">
            <h1 class="my-4">Ver Socios</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Tipo de Socio</th>
                            <th>Actividades</th>
                            <th>Cuota</th>
                            <th>Forma de Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tipo_socio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['actividades']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($row['cuota'], 2)); ?></td>
                                    <td class="<?php echo $row['estado_cuenta'] == 'Efectivo' ? 'status-efectivo' : ($row['estado_cuenta'] == 'Mercado Pago' ? 'status-mercado-pago' : 'status-no-pago'); ?>">
                                        <?php echo htmlspecialchars($row['estado_cuenta']); ?>
                                    </td>
                                    <td><?php echo ''; ?></td> <!-- Columna Estado vacía por el momento -->
                                    <td>
                                        <a href="modificar_socio.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                        <a href="eliminar_socio.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay socios registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>