<?php
session_start();

// ¿Existe la sesión? Si no, fuera de aquí.
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>



<!doctype html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
    <script src="./wwwroot/js/script.js"></script>
  </head>
  <body>
    <header>
      <div class="px-3 py-2 text-bg-primary border-bottom">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none"> 
              <i class="bi bi-bootstrap fw-bold fs-5 pe-2"></i>
            </a>
            <nav>
              <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                <li><a class="nav-link text-white" href="#"> <i class="bi bi-house fw-bold fs-5 pe-2"></i>Home</a></li>
                <li><a class="nav-link text-white" href="#"> <i class="bi bi-box-arrow-in-left fw-bold fs-5 pe-2"></i>Login</a></li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </header>

    <div class="container-fluid">
      <div class="row">
        <aside class="col-8 col-sm-6 col-md-3 col-lg-3 col-xl-2 d-none d-lg-block show"
          style="position: fixed; top: 0;bottom: 0;left: 0;border-right: 1px solid var(--bs-border-color-translucent); margin-top:70px; padding: 15px 0 0;z-index: 999; overflow-y: auto;">
          <div class="px-3">
            <nav>
              <ul class="nav nav-pills flex-column mb-auto">            
                <li class="nav-item">
                  <a class="nav-link active" href="#" onclick="changeTheme('light', './wwwroot/img/bulboff.gif')">
                    <i class="bi bi-lightbulb fw-bold fs-5 pe-2"></i>Apagado
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#" onclick="changeTheme('dark', './wwwroot/img/bulbon.gif')">
                    <i class="bi bi-lightbulb-fill fw-bold fs-5 pe-2"></i>Encendido
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </aside>

        <main class="col-lg-9 col-xl-10 offset-lg-3 offset-xl-2">
          <div class="row">
            <div class="col-12 offset-sm-0 offset-lg-1 col-lg-10 offset-xl-2 col-xl-8 mt-5">
              <article id="article">
                <figure class="text-center">
                  <img id="lightbulb" class="img-fluid" src="./wwwroot/img/bulboff.gif">
                </figure>

                <div class="mt-4 p-3 border rounded">
                  <h5>Agregar Libro</h5>
                  <div class="input-group mb-3">
                    <input type="text" id="titulo" class="form-control" placeholder="Título">
                    <input type="text" id="autor" class="form-control" placeholder="Autor">
                    <button class="btn btn-primary" type="button" onclick="agregarLibro()">Añadir</button>
                  </div>
                </div>

                <table class="table mt-4">
                  <thead>
                    <tr>
                      <th>Título</th>
                      <th>Autor</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="listaLibros">
                    </tbody>
                </table>
              </article>
            </div>
          </div>
        </main>
      </div>
    </div>

    <script src="./js/bootstrap.bundle.min.js"></script>
    
    <script>
      // Función para cambiar tema e imagen
      function changeTheme(mode, imgSrc) {
        document.documentElement.setAttribute('data-bs-theme', mode);
        document.getElementById('lightbulb').src = imgSrc;
        
        // Manejo simple de clase 'active' en el menú
        $('.nav-link').removeClass('active');
        event.currentTarget.classList.add('active');
      }

      // Función para agregar libro (sin campo contenido)
      function agregarLibro() {
        const titulo = document.getElementById('titulo').value;
        const autor = document.getElementById('autor').value;

        if(titulo && autor) {
          const fila = `
            <tr>
              <td>${titulo}</td>
              <td>${autor}</td>
              <td>
                <button class="btn btn-sm btn-warning" onclick="editarLibro(this)">Editar</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarLibro(this)">Eliminar</button>
              </td>
            </tr>`;
          document.getElementById('listaLibros').innerHTML += fila;
          
          // Limpiar campos
          document.getElementById('titulo').value = '';
          document.getElementById('autor').value = '';
        } else {
          alert("Por favor llena el título y el autor");
        }
      }

      function eliminarLibro(btn) {
        btn.closest('tr').remove();
      }

      function editarLibro(btn) {
        const row = btn.closest('tr');
        const nuevoTitulo = prompt("Editar Título", row.cells[0].innerText);
        const nuevoAutor = prompt("Editar Autor", row.cells[1].innerText);
        
        if(nuevoTitulo) row.cells[0].innerText = nuevoTitulo;
        if(nuevoAutor) row.cells[1].innerText = nuevoAutor;
      }
    </script>
  </body>
</html>