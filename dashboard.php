<?php
session_start();

// ¿Existe la sesión? Si no, fuera de aquí.
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>
<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestor de Libros</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
</head>
<body>
  <header>
    <div class="px-3 py-2 text-bg-primary border-bottom">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <a class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
            <i class="bi bi-bootstrap fw-bold fs-5 pe-2"></i>
            <span class="fs-4">Biblioteca Digital</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <div class="container-fluid">
    <div class="row">
      <aside class="col-lg-3 col-xl-2 d-none d-lg-block border-end py-3" 
             style="position: fixed; top: 70px; bottom: 0; left: 0; overflow-y: auto;">
        <div class="px-3">
          <h6>Controles de Luz</h6>
          <nav>
            <ul class="nav nav-pills flex-column mb-auto">
              <li class="nav-item">
                <a id="btn-off" class="nav-link active" href="#" onclick="setTheme('light')">
                  <i class="bi bi-lightbulb fw-bold fs-5 pe-2"></i> Apagado (Luz)
                </a>
              </li>
              <li class="nav-item">
                <a id="btn-on" class="nav-link" href="#" onclick="setTheme('dark')">
                  <i class="bi bi-lightbulb-fill fw-bold fs-5 pe-2"></i> Encendido (Oscuro)
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </aside>

      <main class="col-lg-9 col-xl-10 offset-lg-3 offset-xl-2 p-4">
        <div class="text-center mb-4">
          <figure>
            <img id="lightbulb" class="img-fluid" src="./wwwroot/img/bulboff.gif" style="max-height: 150px;">
          </figure>
        </div>

        <div class="card mb-4">
          <div class="card-header">Añadir Nuevo Libro</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-5">
                <input type="text" id="bookTitle" class="form-control" placeholder="Título del libro">
              </div>
              <div class="col-md-5">
                <input type="text" id="bookAuthor" class="form-control" placeholder="Autor">
              </div>
              <div class="col-md-2">
                <button class="btn btn-success w-100" onclick="addBook()">Añadir</button>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Título</th>
                <th>Autor</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody id="bookList">
              </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="./js/bootstrap.bundle.min.js"></script>
  
  <script>
    // 1. Lógica del Modo Oscuro/Luz
    function setTheme(theme) {
      const htmlElement = document.documentElement;
      const bulbImg = document.getElementById('lightbulb');
      const btnOff = document.getElementById('btn-off');
      const btnOn = document.getElementById('btn-on');

      if (theme === 'dark') {
        htmlElement.setAttribute('data-bs-theme', 'dark');
        bulbImg.src = './wwwroot/img/bulbon.gif';
        btnOn.classList.add('active');
        btnOff.classList.remove('active');
      } else {
        htmlElement.setAttribute('data-bs-theme', 'light');
        bulbImg.src = './wwwroot/img/bulboff.gif';
        btnOff.classList.add('active');
        btnOn.classList.remove('active');
      }
    }

    // 2. Gestión de Libros
    function addBook() {
      const title = document.getElementById('bookTitle').value;
      const author = document.getElementById('bookAuthor').value;

      if (title === '' || author === '') {
        alert("Por favor rellena ambos campos");
        return;
      }

      const tableBody = document.getElementById('bookList');
      const row = document.createElement('tr');

      row.innerHTML = `
        <td>${title}</td>
        <td>${author}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary me-2" onclick="editBook(this)">
            <i class="bi bi-pencil"></i> Editar
          </button>
          <button class="btn btn-sm btn-outline-danger" onclick="deleteBook(this)">
            <i class="bi bi-trash"></i> Eliminar
          </button>
        </td>
      `;

      tableBody.appendChild(row);

      // Limpiar campos
      document.getElementById('bookTitle').value = '';
      document.getElementById('bookAuthor').value = '';
    }

    function deleteBook(btn) {
      if(confirm('¿Seguro que quieres eliminar este libro?')) {
        btn.closest('tr').remove();
      }
    }

    function editBook(btn) {
      const row = btn.closest('tr');
      const title = row.cells[0].innerText;
      const author = row.cells[1].innerText;
      
      const newTitle = prompt("Editar Título:", title);
      const newAuthor = prompt("Editar Autor:", author);
      
      if (newTitle && newAuthor) {
        row.cells[0].innerText = newTitle;
        row.cells[1].innerText = newAuthor;
      }
    }
  </script>
</body>
</html>