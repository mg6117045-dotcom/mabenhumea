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
    $sqlLibros = "SELECT l.id, l.titulo, l.categoria, l.anio_publicacion, a.nombre AS autor 
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

        <div class="row mb-3">
    <div class="col-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLibro">
            <i class="bi bi-book"></i> Nuevo Libro
        </button>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalAutor">
            <i class="bi bi-person-plus"></i> Nuevo Autor
        </button>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPrestamo">
            <i class="bi bi-calendar-plus"></i> Solicitar Préstamo
        </button>
    </div>
</div>

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
                                        <th>Categoria</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($libros as $l): ?>
                                    <tr>
                                        <td><strong><?php echo $l['titulo']; ?></strong></td>
                                        <td><?php echo $l['autor']; ?></td>
                                        <td><?php echo $l['anio_publicacion']; ?></td>
                                        <td><small class="text-muted"><?php echo $l['categoria']; ?></small></td>
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

    <div class="modal fade" id="modalLibro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-book"></i> Registrar Nuevo Libro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="agregar_libro.php" method="POST">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <input type="text" name="categoria" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Año de Publicación</label>
                <input type="number" name="anio" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Seleccionar Autor</label>
                <select name="id_autor" class="form-select" required>
                    <?php
                    $listaAutores = $db->query("SELECT * FROM autores")->fetchAll();
                    foreach($listaAutores as $aut) {
                        echo "<option value='{$aut['id']}'>{$aut['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar Libro</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalAutor" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registrar Autor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="agregar_autor.php" method="POST">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nombre del Autor</label>
                <input type="text" name="nombre_autor" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-secondary">Guardar Autor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalPrestamo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Solicitar Préstamo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="registrar_prestamo.php" method="POST">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Libro a solicitar</label>
                <select name="id_libro" class="form-select" required>
                    <?php foreach($libros as $lib): ?>
                        <option value="<?php echo $lib['id']; ?>"><?php echo $lib['titulo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de Devolución</label>
                <input type="date" name="fecha_devolucion" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-success">Confirmar Préstamo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>