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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $dni = $conn->real_escape_string($_POST['dni']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $email = $conn->real_escape_string($_POST['email']);
    $foto = $_FILES['foto']['name'];
    $edad = (int)$_POST['edad'];
    $tipo_socio_id = (int)$_POST['tipo_socio_id'];
    $actividades = $_POST['actividades']; // Array de actividades seleccionadas
    $estado_cuenta = $conn->real_escape_string($_POST['estado_cuenta']);

    // Datos del adulto responsable
    $adulto_responsable_nombre = $conn->real_escape_string($_POST['adulto_responsable_nombre']);
    $adulto_responsable_apellido = $conn->real_escape_string($_POST['adulto_responsable_apellido']);
    $adulto_responsable_dni = $conn->real_escape_string($_POST['adulto_responsable_dni']);
    $adulto_responsable_telefono = $conn->real_escape_string($_POST['adulto_responsable_telefono']);
    $adulto_responsable_email = $conn->real_escape_string($_POST['adulto_responsable_email']);

    // Guardar la foto en el servidor
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        die("Error al subir la foto.");
    }

    // Insertar datos del socio
    $sql = "INSERT INTO socios (apellido, nombre, dni, telefono, email, foto, edad, tipo_socio_id, estado_cuenta)
            VALUES ('$apellido', '$nombre', '$dni', '$telefono', '$email', '$foto', '$edad', '$tipo_socio_id', '$estado_cuenta')";

    if ($conn->query($sql) === TRUE) {
        $socio_id = $conn->insert_id; // Obtener el ID del socio insertado

        // Insertar actividades del socio
        foreach ($actividades as $actividad_id) {
            $sql_actividad = "INSERT INTO socios_actividades (socio_id, actividad_id) VALUES ('$socio_id', '$actividad_id')";
            if (!$conn->query($sql_actividad)) {
                die("Error al insertar actividades: " . $conn->error);
            }
        }

        // Insertar datos del adulto responsable si la edad es menor de 13 años
        if ($edad < 13) {
            $relacion = 'Adulto Responsable';
            $sql_responsable = "INSERT INTO responsables (nombre, apellido, dni, telefono, email, relacion, socio_id)
                                VALUES ('$adulto_responsable_nombre', '$adulto_responsable_apellido', '$adulto_responsable_dni', '$adulto_responsable_telefono', '$adulto_responsable_email', '$relacion', '$socio_id')";
            if (!$conn->query($sql_responsable)) {
                die("Error al insertar datos del adulto responsable: " . $conn->error);
            }
        }

        header("Location: ver_socios.php");
    } else {
        die("Error al insertar socio: " . $conn->error);
    }

    $conn->close();
} else {
    $sql = "SELECT * FROM tipos_socios";
    $tipos_socios = $conn->query($sql);

    if ($tipos_socios === FALSE) {
        die("Error en la consulta de tipos de socio: " . $conn->error);
    }

    $sql = "SELECT * FROM actividades";
    $actividades = $conn->query($sql);

    if ($actividades === FALSE) {
        die("Error en la consulta de actividades: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Socio</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    </style>
    <script>
        function checkAge() {
            var edad = document.getElementById('edad').value;
            var adultoResponsableSection = document.getElementById('adulto_responsable_section');
            if (edad < 13) {
                adultoResponsableSection.style.display = 'block';
            } else {
                adultoResponsableSection.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkAge();
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center">Gestión de Socios</h3>
        <a href="panel.php">Panel</a>
        <a href="agregar_socio.php">Agregar Socio</a>
        <a href="ver_socios.php">Ver Socios</a>
        <a href="logout.php" class="btn btn-danger btn-block mt-5">Cerrar Sesión</a>
    </div>

    <div class="main-content">
        <div class="container">
            <h1 class="my-4">Agregar Socio</h1>
            <form action="agregar_socio.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="text" class="form-control" id="dni" name="dni" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" class="form-control" id="foto" name="foto" required>
                </div>
                <div class="form-group">
                    <label for="edad">Edad</label>
                    <input type="number" class="form-control" id="edad" name="edad" required onchange="checkAge()">
                </div>
                <div class="form-group">
                    <label for="tipo_socio_id">Tipo de Socio</label>
                    <select class="form-control" id="tipo_socio_id" name="tipo_socio_id" required>
                        <?php while($row = $tipos_socios->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['tipo']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="actividades">Actividades</label>
                    <div>
                        <?php while($row = $actividades->fetch_assoc()): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actividades[]" value="<?php echo htmlspecialchars($row['id']); ?>" id="actividad_<?php echo htmlspecialchars($row['id']); ?>">
                                <label class="form-check-label" for="actividad_<?php echo htmlspecialchars($row['id']); ?>">
                                    <?php echo htmlspecialchars($row['nombre']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="estado_cuenta">Forma de Pago</label>
                    <select class="form-control" id="estado_cuenta" name="estado_cuenta" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Mercado Pago">Mercado Pago</option>
                        <option value="No Pago">No Pago</option>
                    </select>
                </div>
                <div class="form-group" id="adulto_responsable_section" style="display:none;">
                    <label for="adulto_responsable_nombre">Nombre del Adulto Responsable</label>
                    <input type="text" class="form-control" id="adulto_responsable_nombre" name="adulto_responsable_nombre">
                    <label for="adulto_responsable_apellido">Apellido del Adulto Responsable</label>
                    <input type="text" class="form-control" id="adulto_responsable_apellido" name="adulto_responsable_apellido">
                    <label for="adulto_responsable_dni">DNI del Adulto Responsable</label>
                    <input type="text" class="form-control" id="adulto_responsable_dni" name="adulto_responsable_dni">
                    <label for="adulto_responsable_telefono">Teléfono del Adulto Responsable</label>
                    <input type="text" class="form-control" id="adulto_responsable_telefono" name="adulto_responsable_telefono">
                    <label for="adulto_responsable_email">Email del Adulto Responsable</label>
                    <input type="email" class="form-control" id="adulto_responsable_email" name="adulto_responsable_email">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Agregar Socio</button>
            </form>
        </div>
    </div>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>