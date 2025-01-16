<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "srv650.hstgr.io";
$username = "u704891060_Hermanos2025";
$password = "Hermanos_2025";
$dbname = "u704891060_club_telefonos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $apellido = $_POST['apellido'];
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $foto = $_POST['foto'];
    $edad = $_POST['edad'];
    $tipo_socio_id = $_POST['tipo_socio_id'];
    $actividades = isset($_POST['actividades']) ? $_POST['actividades'] : [];
    $estado_cuenta = $_POST['estado_cuenta'];
    $adulto_nombre = $_POST['adulto_nombre'] ?? null;
    $adulto_apellido = $_POST['adulto_apellido'] ?? null;
    $adulto_dni = $_POST['adulto_dni'] ?? null;
    $adulto_telefono = $_POST['adulto_telefono'] ?? null;
    $adulto_email = $_POST['adulto_email'] ?? null;

    // Actualizar datos del socio
    $sql = "UPDATE socios SET 
                apellido='$apellido', 
                nombre='$nombre', 
                dni='$dni', 
                telefono='$telefono', 
                email='$email', 
                foto='$foto', 
                edad='$edad', 
                tipo_socio_id='$tipo_socio_id', 
                estado_cuenta='$estado_cuenta' 
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // Actualizar actividades del socio
        $conn->query("DELETE FROM socios_actividades WHERE socio_id='$id'");
        foreach ($actividades as $actividad_id) {
            $conn->query("INSERT INTO socios_actividades (socio_id, actividad_id) VALUES ('$id', '$actividad_id')");
        }

        // Actualizar datos del adulto responsable si la edad es menor de 13 años
        if ($edad < 13) {
            $sql_responsable = "REPLACE INTO responsables 
                        (socio_id, nombre, apellido, dni, telefono, email) 
                        VALUES ('$id', '$adulto_nombre', '$adulto_apellido', '$adulto_dni', '$adulto_telefono', '$adulto_email')";
            $conn->query($sql_responsable);
        } else {
            $conn->query("DELETE FROM responsables WHERE socio_id='$id'");
        }

        // Redirigir a la página de ver socios
        header("Location: ver_socios.php");
        exit;
    } else {
        echo "Error al actualizar el registro: " . $conn->error;
    }

    $conn->close();
} else {
    $id = $_GET['id'];

    $sql = "SELECT * FROM socios WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontró el socio.";
        exit;
    }

    $sql = "SELECT * FROM tipos_socios";
    $tipos_socios = $conn->query($sql);

    $sql = "SELECT * FROM actividades";
    $actividades = $conn->query($sql);

    $sql = "SELECT actividad_id FROM socios_actividades WHERE socio_id='$id'";
    $result_actividades = $conn->query($sql);
    $socio_actividades = [];
    while ($row_actividad = $result_actividades->fetch_assoc()) {
        $socio_actividades[] = $row_actividad['actividad_id'];
    }

    // Cargar datos del adulto responsable si existe
    $sql = "SELECT * FROM responsables WHERE socio_id = $id";
    $responsable_result = $conn->query($sql);
    $responsable = $responsable_result->fetch_assoc();
}
?>

<?php include('header.php'); ?>

<h1 class="my-4">Modificar Socio</h1>
<form action="modificar_socio.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <div class="form-group">
        <label for="apellido">Apellido</label>
        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($row['apellido']); ?>" required>
    </div>
    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
    </div>
    <div class="form-group">
        <label for="dni">DNI</label>
        <input type="text" class="form-control" id="dni" name="dni" value="<?php echo htmlspecialchars($row['dni']); ?>" required>
    </div>
    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($row['telefono']); ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
    </div>
    <div class="form-group">
        <label for="foto">Foto</label>
        <input type="text" class="form-control" id="foto" name="foto" value="<?php echo htmlspecialchars($row['foto']); ?>" required>
    </div>
    <div class="form-group">
        <label for="edad">Edad</label>
        <input type="number" class="form-control" id="edad" name="edad" value="<?php echo htmlspecialchars($row['edad']); ?>" required onchange="checkAge()">
    </div>
    <div class="form-group">
        <label for="tipo_socio_id">Tipo de Socio</label>
        <select class="form-control" id="tipo_socio_id" name="tipo_socio_id" required>
            <?php while($tipo = $tipos_socios->fetch_assoc()): ?>
                <option value="<?php echo $tipo['id']; ?>" <?php if ($row['tipo_socio_id'] == $tipo['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($tipo['tipo']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Actividad</label>
        <?php while($actividad = $actividades->fetch_assoc()): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="actividades[]" value="<?php echo $actividad['id']; ?>" id="actividad_<?php echo $actividad['id']; ?>" <?php if (in_array($actividad['id'], $socio_actividades)) echo 'checked'; ?>>
                <label class="form-check-label" for="actividad_<?php echo $actividad['id']; ?>">
                    <?php echo htmlspecialchars($actividad['nombre']); ?>
                </label>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="form-group">
        <label for="estado_cuenta">Estado de Cuenta</label>
        <select class="form-control" id="estado_cuenta" name="estado_cuenta" required>
            <option value="Efectivo" <?php if ($row['estado_cuenta'] == 'Efectivo') echo 'selected'; ?>>Efectivo</option>
            <option value="Mercado Pago" <?php if ($row['estado_cuenta'] == 'Mercado Pago') echo 'selected'; ?>>Mercado Pago</option>
            <option value="No Pago" <?php if ($row['estado_cuenta'] == 'No Pago') echo 'selected'; ?>>No Pago</option>
        </select>
    </div>
    <div id="adulto_responsable_section" style="display: none;">
        <h4>Datos del Adulto Responsable</h4>
        <div class="form-group">
            <label for="adulto_nombre">Nombre del Adulto Responsable</label>
            <input type="text" class="form-control" id="adulto_nombre" name="adulto_nombre" value="<?php echo htmlspecialchars($responsable['nombre'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adulto_apellido">Apellido del Adulto Responsable</label>
            <input type="text" class="form-control" id="adulto_apellido" name="adulto_apellido" value="<?php echo htmlspecialchars($responsable['apellido'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adulto_dni">DNI del Adulto Responsable</label>
            <input type="text" class="form-control" id="adulto_dni" name="adulto_dni" value="<?php echo htmlspecialchars($responsable['dni'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adulto_telefono">Teléfono del Adulto Responsable</label>
            <input type="text" class="form-control" id="adulto_telefono" name="adulto_telefono" value="<?php echo htmlspecialchars($responsable['telefono'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adulto_email">Email del Adulto Responsable</label>
            <input type="email" class="form-control" id="adulto_email" name="adulto_email" value="<?php echo htmlspecialchars($responsable['email'] ?? ''); ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
</form>

<?php include('footer.php'); ?>