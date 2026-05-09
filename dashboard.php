<?php
session_start();

// 1. PROTECCIÓN DE RUTA
if(!isset($_SESSION['id_usuario']) && !isset($_COOKIE["id_usuario"])) {
    header("Location: index.php");
    exit();
}

// Si existe la cookie pero no la sesión, la restauramos
if(!isset($_SESSION['id_usuario']) && isset($_COOKIE["id_usuario"])) {
    $_SESSION['id_usuario'] = $_COOKIE["id_usuario"];
}

require_once 'db.php';
$db = conectarDB();
$id_actual = $_SESSION['id_usuario'];

try {
    // 2. CONSULTA DE LIBROS Y AUTORES
    $sqlLibros = "SELECT l.id, l.titulo, l.isbn, l.anio_publicacion, a.nombre AS autor 
                  FROM libros l 
                  INNER JOIN autores a ON l.id_autor = a.id";
    $libros = $db->query($sqlLibros)->fetchAll();

    // 3. CONSULTA DE PRÉSTAMOS (Solo los del usuario actual)
    $sqlPrestamos = "SELECT p.id, l.titulo AS libro, p.fecha_prestamo, p.fecha_devolucion, p.devuelto 
                     FROM prestamos p
                     INNER JOIN libros l ON p.id_libro = l.id
                     WHERE p.id_usuario = :id_user";
    $stmtP = $db->prepare($sqlPrestamos);
    $stmtP->execute(['id_user' => $id_actual]);
    $prestamos = $stmtP->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Biblioteca</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
</head>
<body>
    <header class="px-3 py-2 text-bg-primary border-bottom">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <a class="d-flex align-items-center text-white text-decoration-none fw-bold">
                    <i class="bi bi-book-half fs-4 pe-2"></i> BIBLIOTECA VIRTUAL
                </a>
                <a href="logout.php" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-left"></i> Salir
                </a>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12 col-xl-7 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Catálogo de Libros</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>Año</th>
                                        <th>ISBN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($libros as $l): ?>
                                    <tr>
                                        <td><strong><?php echo $l['titulo']; ?></strong></td>
                                        <td><?php echo $l['autor']; ?></td>
                                        <td><?php echo $l['anio_publicacion']; ?></td>
                                        <td><small class="text-muted"><?php echo $l['isbn']; ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5 mb-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Mis Préstamos</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($prestamos)): ?>
                            <p class="text-center text-muted">No tienes préstamos activos.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Libro</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($prestamos as $p): ?>
                                        <tr>
                                            <td><?php echo $p['libro']; ?></td>
                                            <td><small><?php echo date('d/m/Y', strtotime($p['fecha_prestamo'])); ?></small></td>
                                            <td>
                                                <?php if($p['devuelto']): ?>
                                                    <span class="badge bg-success">Devuelto</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6>Simulador de Lectura</h6>
                        <img id="lightbulb" src="./wwwroot/img/bulboff.gif" class="img-fluid mb-2" style="max-height: 100px;">
                        <div>
                            <button class="btn btn-sm btn-outline-warning" onclick="document.getElementById('lightbulb').src='./wwwroot/img/bulbon.gif'">Encender</button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('lightbulb').src='./wwwroot/img/bulboff.gif'">Apagar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLibro"> + Nuevo Libro </button>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalAutor"> + Nuevo Autor </button>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPrestamo"> + Nuevo Préstamo </button>
</div>
    <script src="./wwwroot/js/bootstrap.bundle.min.js"></script>
</body>
</html>