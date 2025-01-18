<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$servername = "srv650.hstgr.io";
$username = "u704891060_Hermanos2025";
$password = "Hermanos_2025";
$dbname = "u704891060_club_telefonos";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$apellido = $_POST['apellido'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$edad = $_POST['edad'];
$tipo_socio_id = $_POST['tipo_socio'];
$actividades_ids = isset($_POST['actividades']) ? $_POST['actividades'] : []; // Esto debe ser un array de IDs de actividades seleccionadas

// Manejar la subida de la foto
$foto = $_FILES['foto']['name'];
$target_dir = "uploads/";
$target_file = $target_dir . basename($foto);

// Verificar si el directorio `uploads` existe y tiene permisos de escritura
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
    // Insertar los datos en la tabla de socios
    $sql = "INSERT INTO socios (apellido, nombre, dni, telefono, email, foto, edad, tipo_socio_id)
            VALUES ('$apellido', '$nombre', '$dni', '$telefono', '$email', '$target_file', '$edad', '$tipo_socio_id')";

    if ($conn->query($sql) === TRUE) {
        // Obtener el ID del nuevo socio
        $socio_id = $conn->insert_id;

        // Insertar las actividades del socio en la tabla intermedia
        if (!empty($actividades_ids)) {
            foreach ($actividades_ids as $actividad_id) {
                $sql_actividad = "INSERT INTO socios_actividades (socio_id, actividad_id)
                                  VALUES ('$socio_id', '$actividad_id')";
                $conn->query($sql_actividad);
            }
        }

        // Si el socio es menor de 13 años, insertar los datos del responsable
        if ($edad < 13) {
            $responsable_nombre = $_POST['responsable_nombre'];
            $responsable_apellido = $_POST['responsable_apellido'];
            $responsable_dni = $_POST['responsable_dni'];
            $responsable_telefono = $_POST['responsable_telefono'];
            $responsable_email = $_POST['responsable_email'];
            $responsable_relacion = $_POST['responsable_relacion'];
            $responsable_telefono_emergencia = $_POST['responsable_telefono_emergencia'];

            $sql_responsable = "INSERT INTO responsables (nombre, apellido, dni, telefono, email, relacion, telefono_emergencia, socio_id)
                                VALUES ('$responsable_nombre', '$responsable_apellido', '$responsable_dni', '$responsable_telefono', '$responsable_email', '$responsable_relacion', '$responsable_telefono_emergencia', '$socio_id')";

            if ($conn->query($sql_responsable) === TRUE) {
                echo "Nuevo socio y responsable agregados exitosamente";
            } else {
                echo "Error: " . $sql_responsable . "<br>" . $conn->error;
            }
        } else {
            echo "Nuevo socio agregado exitosamente";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error al subir la foto.";
}

// Calcular el precio total
$precio_total = 0;

// Obtener el precio del tipo de socio
$sql_tipo_socio = "SELECT monto FROM tipos_socios WHERE id = '$tipo_socio_id'";
$result_tipo_socio = $conn->query($sql_tipo_socio);
if ($result_tipo_socio->num_rows > 0) {
    $row_tipo_socio = $result_tipo_socio->fetch_assoc();
    $precio_total += $row_tipo_socio['monto'];
}

// Obtener los precios de las actividades
if (!empty($actividades_ids)) {
    foreach ($actividades_ids as $actividad_id) {
        $sql_actividad = "SELECT precio FROM actividades WHERE id = '$actividad_id'";
        $result_actividad = $conn->query($sql_actividad);
        if ($result_actividad->num_rows > 0) {
            $row_actividad = $result_actividad->fetch_assoc();
            $precio_total += $row_actividad['precio'];
        }
    }
}

echo "El precio total a abonar es: $" . $precio_total;

$conn->close();
?>