<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Gestión de Socios'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
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
        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
            }
            .sidebar {
                width: 100%;
                position: relative;
            }
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
            <a href="pagos.php" class="pl-3">Pagos</a> <!-- Nuevo submenú -->
        </div>
        <a href="#visualizar" data-toggle="collapse">Visualizar</a>
        <div id="visualizar" class="collapse">
            <a href="ver_socios.php" class="pl-3">Ver Socios</a>
            <a href="#" class="pl-3">Quinchos Alquilados</a>
            <a href="#" class="pl-3">Estacionamientos</a>
            <a href="#" class="pl-3">Colonia de Vacaciones</a>
        </div>
        <a href="logout.php" class="btn btn-danger btn-block mt-5">Cerrar Sesión</a>
    </div>
    <div class="main-content">
        <div class="container">