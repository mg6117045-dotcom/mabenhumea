<?php
session_start();

// ¿Existe la sesión? Si no, fuera de aquí.
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca Virtual - Mis Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --bg-color: #ffffff;
      --text-color: #212529;
      --sidebar-bg: #f8f9fa;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
    }

    body.dark-mode {
      --bg-color: #121212;
      --text-color: #e0e0e0;
      --sidebar-bg: #1e1e1e;
    }

    .sidebar {
      position: fixed;
      top: 70px;
      bottom: 0;
      left: 0;
      border-right: 1px solid rgba(0,0,0,0.1);
      padding: 15px 0 0;
      z-index: 999;
      overflow-y: auto;
      background-color: var(--sidebar-bg);
    }

    .book-card {
      transition: transform 0.2s, box-shadow 0.3s;
      cursor: pointer;
      height: 100%;
      border: none;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }
    
    .book-card.color-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .book-card.color-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .book-card.color-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .book-card.color-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
    
    .book-cover {
      height: 250px;
      object-fit: cover;
      background-color: #eee;
    }
    
    main { margin-top: 20px; margin-bottom: 40px; }
  </style>
</head>
<body>

<header>
  <div class="px-3 py-2 text-bg-primary border-bottom fixed-top">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <a class="d-flex align-items-center my-2 my-lg-0 text-white text-decoration-none" style="cursor:pointer" onclick="showBooks()">
          <i class="bi bi-book-half fw-bold fs-3 pe-2"></i>
          <span class="fs-4">Mi Biblioteca</span>
        </a>
        <nav>
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li><a class="nav-link text-white" style="cursor:pointer" onclick="showBooks()"><i class="bi bi-collection pe-1"></i>Libros</a></li>
            <li><a class="nav-link text-white" style="cursor:pointer" onclick="showAddBookForm()"><i class="bi bi-plus-circle pe-1"></i>Agregar</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</header>

<div class="container-fluid" style="margin-top: 70px;">
  <div class="row">
    <aside class="col-md-3 col-lg-2 sidebar">
      <div class="px-3">
        <div class="mb-4">
          <h6><i class="bi bi-circle-half"></i> Tema </h6>
          <div class="btn-group w-100" role="group">
            <button class="btn btn-sm btn-outline-secondary" onclick="setLightMode('off')">Oscuro</button>
            <button class="btn btn-sm btn-outline-warning" onclick="setLightMode('on')">Luz</button>
          </div>
        </div>
        <hr>
        <div id="categories-list">
          <h6><i class="bi bi-bookmark"></i> Categorías</h6>
          </div>
      </div>
    </aside>

    <main class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
      <div id="main-content" class="container mt-4">
        <div class="row" id="books-container"></div>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  class Libro {
    constructor(id, titulo, autor, descripcion, portada, categoria = "General") {
      this.id = id;
      this.titulo = titulo;
      this.autor = autor;
      this.descripcion = descripcion;
      this.portada = portada || "https://via.placeholder.com/200x250?text=Sin+Portada";
      this.categoria = categoria;
    }
  }

  class BibliotecaVirtual {
    constructor() {
      this.libros = [];
      this.cargarDesdeLocalStorage();
    }

    guardarEnLocalStorage() {
      localStorage.setItem('biblioteca_libros', JSON.stringify(this.libros));
    }

    cargarDesdeLocalStorage() {
      const guardados = localStorage.getItem('biblioteca_libros');
      // Si existen datos, los carga; si no, deja el array vacío.
      this.libros = guardados ? JSON.parse(guardados) : [];
    }

    agregarLibro(titulo, autor, descripcion, portada, categoria) {
      const nuevoId = Date.now();
      const nuevoLibro = new Libro(nuevoId, titulo, autor, descripcion, portada, categoria);
      this.libros.push(nuevoLibro);
      this.guardarEnLocalStorage();
    }

    obtenerTodos() { return this.libros; }
    
    obtenerCategorias() {
      const cats = new Set(this.libros.map(l => l.categoria));
      return ['Todas', ...Array.from(cats).sort()];
    }
  }

  const biblioteca = new BibliotecaVirtual();

  function getColorClass(id) {
    const colors = ['color-1', 'color-2', 'color-3', 'color-4'];
    return colors[id % colors.length];
  }

  // Función principal para renderizar libros (opcionalmente filtrados)
  function renderBooks(listaLibros) {
    const container = document.getElementById('books-container');
    
    if (listaLibros.length === 0) {
      container.innerHTML = '<div class="text-center mt-5"><h3>No hay libros en esta sección.</h3><p>Haz clic en "Agregar" para empezar.</p></div>';
    } else {
      container.innerHTML = listaLibros.map(libro => `
        <div class="col-md-4 mb-4">
          <div class="card book-card ${getColorClass(libro.id)}">
            <img src="${libro.portada}" class="card-img-top book-cover" alt="portada">
            <div class="card-body">
              <h5 class="card-title">${libro.titulo}</h5>
              <h6 class="card-subtitle mb-2">${libro.autor}</h6>
              <p class="card-text small">${libro.descripcion}</p>
              <span class="badge bg-dark">${libro.categoria}</span>
            </div>
          </div>
        </div>
      `).join('');
    }
    actualizarSidebarCategorias();
  }

  function showBooks() {
    renderBooks(biblioteca.obtenerTodos());
  }

  // --- SOLUCIÓN AL PROBLEMA DE FILTRADO ---
  function filtrar(cat) {
    if (cat === 'Todas') {
      showBooks();
    } else {
      const filtrados = biblioteca.obtenerTodos().filter(l => l.categoria === cat);
      renderBooks(filtrados);
    }
  }

  function showAddBookForm() {
    document.getElementById('books-container').innerHTML = `
      <div class="col-md-8 mx-auto">
        <div class="card p-4 shadow-sm" style="color: #333">
          <h4>Nuevo Libro</h4>
          <form id="addBookForm">
            <input type="text" class="form-control mb-2" id="titulo" placeholder="Título" required>
            <input type="text" class="form-control mb-2" id="autor" placeholder="Autor" required>
            <input type="text" class="form-control mb-2" id="categoria" placeholder="Categoría (ej: Fantasía, Programación)">
            <input type="url" class="form-control mb-2" id="portada" placeholder="URL de imagen de portada">
            <textarea class="form-control mb-3" id="descripcion" placeholder="Descripción breve" rows="3" required></textarea>
            <button type="submit" class="btn btn-primary">Guardar Libro</button>
            <button type="button" class="btn btn-secondary" onclick="showBooks()">Cancelar</button>
          </form>
        </div>
      </div>
    `;

    document.getElementById('addBookForm').addEventListener('submit', function(e) {
      e.preventDefault();
      biblioteca.agregarLibro(
        document.getElementById('titulo').value,
        document.getElementById( 'autor').value,
        document.getElementById('descripcion').value,
        document.getElementById('portada').value,
        document.getElementById('categoria').value || "General"
      );
      showBooks();
    });
  }

  function actualizarSidebarCategorias() {
    const categorias = biblioteca.obtenerCategorias();
    const container = document.getElementById('categories-list');
    
    // Solo mostramos categorías si hay libros
    if(biblioteca.obtenerTodos().length === 0) {
        container.innerHTML = '<h6><i class="bi bi-bookmark"></i> Categorías</h6><p class="small text-muted ps-2">Sin categorías</p>';
        return;
    }

    container.innerHTML = '<h6><i class="bi bi-bookmark"></i> Categorías</h6>' + categorias.map(cat => `
      <a class="d-block mb-1 text-decoration-none text-capitalize" style="cursor:pointer; color: inherit;" onclick="filtrar('${cat}')">
        <i class="bi bi-tag small"></i> ${cat}
      </a>
    `).join('');
  }

  function setLightMode(mode) {
    if (mode === 'on') {
      document.body.classList.remove('dark-mode');
    } else {
      document.body.classList.add('dark-mode');
    }
  }

  // Iniciar la aplicación
  showBooks();
</script>
</body>
</html>