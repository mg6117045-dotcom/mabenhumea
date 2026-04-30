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
  <title>Biblioteca Virtual</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .book-card {
      transition: transform 0.2s;
      cursor: pointer;
      height: 100%;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .book-cover {
      height: 250px;
      object-fit: cover;
      background-color: #f8f9fa;
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
      background-color: #f8f9fa;
    }
    main {
      margin-top: 20px;
      margin-bottom: 40px;
    }
    .book-content {
      max-height: 70vh;
      overflow-y: auto;
      white-space: pre-wrap;
      font-family: Georgia, serif;
      line-height: 1.6;
    }
    .navbar-brand i {
      font-size: 1.5rem;
    }
  </style>
</head>
<body>

<header>
  <div class="px-3 py-2 text-bg-primary border-bottom fixed-top">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
          <i class="bi bi-book-half fw-bold fs-3 pe-2"></i>
          <span class="fs-4">Biblioteca Virtual</span>
        </a>
        <nav>
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li><a class="nav-link text-white" href="#" onclick="showBooks()"><i class="bi bi-collection fw-bold fs-5 pe-2"></i>Libros</a></li>
            <li><a class="nav-link text-white" href="#" onclick="showAddBookForm()"><i class="bi bi-plus-circle fw-bold fs-5 pe-2"></i>Agregar</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</header>

<div class="container-fluid" style="margin-top: 70px;">
  <div class="row">
    <!-- Sidebar -->
    <aside class="col-8 col-sm-6 col-md-3 col-lg-3 col-xl-2 sidebar">
      <div class="px-3">
        <div class="mb-4">
          <h6><i class="bi bi-lightbulb"></i> Modo luz</h6>
          <div class="btn-group w-100" role="group">
            <button class="btn btn-sm btn-outline-secondary" onclick="setLightMode('off')">
              <i class="bi bi-lightbulb"></i> Apagado
            </button>
            <button class="btn btn-sm btn-outline-warning" onclick="setLightMode('on')">
              <i class="bi bi-lightbulb-fill"></i> Encendido
            </button>
          </div>
        </div>
        <hr>
        <div class="mb-3">
          <h6><i class="bi bi-info-circle"></i> Estadísticas</h6>
          <small class="text-muted" id="stats">Cargando...</small>
        </div>
        <hr>
        <div>
          <h6><i class="bi bi-bookmark"></i> Categorías</h6>
          <div id="categories-list"></div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="col-lg-9 col-xl-10 offset-lg-3 offset-xl-2">
      <div id="main-content" class="container mt-4">
        <!-- Aquí se mostrarán los libros o el formulario -->
        <div class="row" id="books-container"></div>
      </div>
    </main>
  </div>
</div>

<!-- Modal para leer libro -->
<div class="modal fade" id="readModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitle">Lectura</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="modalContent" class="book-content"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Clase Libro
  class Libro {
    constructor(id, titulo, autor, descripcion, contenido, portada, categoria = "General") {
      this.id = id;
      this.titulo = titulo;
      this.autor = autor;
      this.descripcion = descripcion;
      this.contenido = contenido; // Texto completo del libro
      this.portada = portada || "https://via.placeholder.com/200x250?text=Sin+Portada";
      this.categoria = categoria;
      this.fechaAgregado = new Date().toISOString();
    }
  }

  // Gestor de Biblioteca
  class BibliotecaVirtual {
    constructor() {
      this.libros = [];
      this.cargarLibrosIniciales();
      this.cargarDesdeLocalStorage();
    }

    cargarLibrosIniciales() {
      const librosIniciales = [
        new Libro(1, "El Principito", "Antoine de Saint-Exupéry", 
          "Un clásico de la literatura infantil que invita a reflexionar sobre la amistad y el amor.",
          "Había una vez un principito que vivía en un asteroide... [Aquí iría el contenido completo del libro]",
          "https://images.cdn3.buscalibre.com/fit-in/300x300/61/8e/618e227e605727fc26d7d132b1b2e6bb.jpg",
          "Ficción"),
        new Libro(2, "Cien años de soledad", "Gabriel García Márquez", 
          "La historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo.",
          "Muchos años después, frente al pelotón de fusilamiento...",
          "https://images.cdn3.buscalibre.com/fit-in/300x300/be/75/be75f4a9de6bcceb7c8c26b776b658fa.jpg",
          "Realismo mágico"),
        new Libro(3, "1984", "George Orwell", 
          "Una distopía que explora los peligros del totalitarismo y la vigilancia masiva.",
          "Era un brillante día de abril y los relojes daban las trece...",
          "https://images.cdn1.buscalibre.com/fit-in/300x300/df/61/df61beca7c0fcb48f77b4db37540e612.jpg",
          "Ciencia ficción")
      ];
      this.libros = librosIniciales;
    }

    guardarEnLocalStorage() {
      localStorage.setItem('biblioteca_libros', JSON.stringify(this.libros));
    }

    cargarDesdeLocalStorage() {
      const guardados = localStorage.getItem('biblioteca_libros');
      if (guardados) {
        const parsed = JSON.parse(guardados);
        this.libros = parsed.map(l => Object.assign(new Libro(), l));
        this.asignarIds();
      } else {
        this.guardarEnLocalStorage();
      }
      this.actualizarEstadisticas();
    }

    asignarIds() {
      let maxId = 0;
      this.libros.forEach(libro => {
        if (libro.id > maxId) maxId = libro.id;
      });
      this.proximoId = maxId + 1;
    }

    agregarLibro(titulo, autor, descripcion, contenido, portada, categoria) {
      const nuevoId = this.libros.length > 0 ? Math.max(...this.libros.map(l => l.id)) + 1 : 4;
      const nuevoLibro = new Libro(nuevoId, titulo, autor, descripcion, contenido, portada, categoria);
      this.libros.push(nuevoLibro);
      this.guardarEnLocalStorage();
      this.actualizarEstadisticas();
      return nuevoLibro;
    }

    obtenerTodos() {
      return this.libros;
    }

    obtenerPorCategoria(categoria) {
      if (categoria === 'Todas') return this.libros;
      return this.libros.filter(l => l.categoria === categoria);
    }

    obtenerCategorias() {
      const cats = new Set(this.libros.map(l => l.categoria));
      return ['Todas', ...Array.from(cats).sort()];
    }

    actualizarEstadisticas() {
      const stats = document.getElementById('stats');
      if (stats) {
        stats.innerHTML = `📚 ${this.libros.length} libros<br>📖 ${this.libros.filter(l => l.contenido && l.contenido.length > 50).length} con contenido<br>⭐ ${Math.floor(Math.random() * 100)} lecturas totales`;
      }
    }
  }

  // Inicializar biblioteca
  const biblioteca = new BibliotecaVirtual();
  let modoLuz = 'off'; // off = normal, on = fondo claro

  // Funciones de UI
  function showBooks() {
    const container = document.getElementById('books-container');
    if (!container) return;
    
    const libros = biblioteca.obtenerTodos();
    container.innerHTML = `
      <div class="row">
        ${libros.map(libro => `
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card book-card h-100" onclick="readBook(${libro.id})">
              <img src="${libro.portada}" class="card-img-top book-cover" alt="${libro.titulo}" onerror="this.src='https://via.placeholder.com/200x250?text=Portada+no+disponible'">
              <div class="card-body">
                <h5 class="card-title">${libro.titulo}</h5>
                <h6 class="card-subtitle mb-2 text-muted">${libro.autor}</h6>
                <p class="card-text">${libro.descripcion.substring(0, 100)}${libro.descripcion.length > 100 ? '...' : ''}</p>
                <span class="badge bg-secondary">${libro.categoria}</span>
              </div>
              <div class="card-footer bg-transparent">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); readBook(${libro.id})">
                  <i class="bi bi-book"></i> Leer
                </button>
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
    
    // Actualizar sidebar con categorías
    actualizarSidebarCategorias();
  }

  function actualizarSidebarCategorias() {
    const categorias = biblioteca.obtenerCategorias();
    const container = document.getElementById('categories-list');
    if (container) {
      container.innerHTML = categorias.map(cat => `
        <a href="#" class="d-block text-decoration-none mb-1" onclick="filtrarPorCategoria('${cat}')">
          <i class="bi bi-tag"></i> ${cat}
        </a>
      `).join('');
    }
  }

  function filtrarPorCategoria(categoria) {
    const libros = categoria === 'Todas' ? biblioteca.obtenerTodos() : biblioteca.obtenerPorCategoria(categoria);
    const container = document.getElementById('books-container');
    if (container) {
      container.innerHTML = `
        <div class="row">
          ${libros.map(libro => `
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="card book-card h-100">
                <img src="${libro.portada}" class="card-img-top book-cover" alt="${libro.titulo}" onerror="this.src='https://via.placeholder.com/200x250?text=Portada+no+disponible'">
                <div class="card-body">
                  <h5 class="card-title">${libro.titulo}</h5>
                  <h6 class="card-subtitle mb-2 text-muted">${libro.autor}</h6>
                  <p class="card-text">${libro.descripcion.substring(0, 100)}${libro.descripcion.length > 100 ? '...' : ''}</p>
                  <span class="badge bg-secondary">${libro.categoria}</span>
                </div>
                <div class="card-footer">
                  <button class="btn btn-sm btn-primary" onclick="readBook(${libro.id})">
                    <i class="bi bi-book"></i> Leer
                  </button>
                </div>
              </div>
            </div>
          `).join('')}
        </div>
      `;
    }
  }

  function showAddBookForm() {
    const container = document.getElementById('books-container');
    container.innerHTML = `
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h4><i class="bi bi-plus-circle"></i> Agregar nuevo libro</h4>
            </div>
            <div class="card-body">
              <form id="addBookForm">
                <div class="mb-3">
                  <label class="form-label">Título *</label>
                  <input type="text" class="form-control" id="titulo" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Autor *</label>
                  <input type="text" class="form-control" id="autor" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Categoría</label>
                  <input type="text" class="form-control" id="categoria" placeholder="Ej: Novela, Poesía, Ciencia...">
                </div>
                <div class="mb-3">
                  <label class="form-label">URL de portada (opcional)</label>
                  <input type="url" class="form-control" id="portada" placeholder="https://ejemplo.com/imagen.jpg">
                </div>
                <div class="mb-3">
                  <label class="form-label">Descripción breve *</label>
                  <textarea class="form-control" id="descripcion" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Contenido del libro *</label>
                  <textarea class="form-control" id="contenido" rows="8" placeholder="Escribe aquí el texto completo del libro..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar libro</button>
                <button type="button" class="btn btn-secondary" onclick="showBooks()"><i class="bi bi-arrow-left"></i> Cancelar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    `;
    
    document.getElementById('addBookForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const titulo = document.getElementById('titulo').value;
      const autor = document.getElementById('autor').value;
      const descripcion = document.getElementById('descripcion').value;
      const contenido = document.getElementById('contenido').value;
      const portada = document.getElementById('portada').value || "https://via.placeholder.com/200x250?text=Nuevo+Libro";
      const categoria = document.getElementById('categoria').value || "General";
      
      biblioteca.agregarLibro(titulo, autor, descripcion, contenido, portada, categoria);
      alert(`📘 Libro "${titulo}" agregado correctamente`);
      showBooks();
    });
  }

  function readBook(id) {
    const libro = biblioteca.libros.find(l => l.id === id);
    if (!libro) return;
    
    document.getElementById('modalTitle').innerHTML = `<i class="bi bi-book"></i> ${libro.titulo} - ${libro.autor}`;
    document.getElementById('modalContent').innerHTML = `
      <div class="mb-3">
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> ${libro.descripcion}
        </div>
        <hr>
        <h5>Contenido:</h5>
        <p>${libro.contenido || "Este libro aún no tiene contenido disponible."}</p>
      </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('readModal'));
    modal.show();
  }

  function setLightMode(mode) {
    modoLuz = mode;
    const body = document.body;
    if (mode === 'on') {
      body.style.backgroundColor = '#fff9e6';
      body.style.color = '#333';
      document.querySelectorAll('.card').forEach(c => c.style.backgroundColor = '#fffef7');
    } else {
      body.style.backgroundColor = '';
      body.style.color = '';
      document.querySelectorAll('.card').forEach(c => c.style.backgroundColor = '');
    }
  }

  // Inicializar vista
  showBooks();
</script>

</body>
</html>S