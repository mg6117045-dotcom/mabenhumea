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
  <title>Biblioteca Virtual - Guarda tus libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* Transiciones suaves para el modo oscuro */
    body {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .book-card {
      transition: transform 0.2s, background-color 0.3s, box-shadow 0.3s;
      cursor: pointer;
      height: 100%;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    /* Colores dinámicos para las tarjetas */
    .book-card.color-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .book-card.color-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .book-card.color-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .book-card.color-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
    .book-card.color-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .book-card.color-6 { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: white; }
    .book-card.color-7 { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333; }
    .book-card.color-8 { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; }
    
    .book-card .card-title,
    .book-card .card-subtitle,
    .book-card .card-text,
    .book-card .badge {
      color: inherit;
    }
    .book-card .card-subtitle {
      opacity: 0.9;
    }
    .book-card .badge {
      background-color: rgba(0,0,0,0.3) !important;
    }
    .book-card .btn-primary {
      background-color: rgba(255,255,255,0.3);
      border-color: rgba(255,255,255,0.5);
      color: white;
    }
    .book-card .btn-primary:hover {
      background-color: rgba(255,255,255,0.5);
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
      transition: background-color 0.3s ease, border-color 0.3s ease;
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
    .book-card img {
      border-bottom: 2px solid rgba(255,255,255,0.3);
    }
    
    /* Estilos para modo oscuro */
    body.dark-mode {
      background-color: #1a1a2e;
      color: #eee;
    }
    
    body.dark-mode .sidebar {
      background-color: #16213e;
      border-right-color: rgba(255,255,255,0.1);
    }
    
    body.dark-mode .text-muted {
      color: #aaa !important;
    }
    
    body.dark-mode .card {
      background-color: #0f3460;
      color: #eee;
    }
    
    body.dark-mode .modal-content {
      background-color: #16213e;
      color: #eee;
    }
    
    body.dark-mode .modal-header {
      border-bottom-color: #0f3460;
    }
    
    body.dark-mode .btn-close {
      filter: invert(1);
    }
    
    body.dark-mode .alert-info {
      background-color: #0f3460;
      border-color: #1a5a8a;
      color: #eee;
    }
    
    body.dark-mode .alert-secondary {
      background-color: #1a1a2e;
      border-color: #2a2a3e;
      color: #eee;
    }
    
    body.dark-mode .form-control {
      background-color: #0f3460;
      border-color: #0a2848;
      color: #eee;
    }
    
    body.dark-mode .form-control:focus {
      background-color: #0f3460;
      color: #eee;
    }
    
    body.dark-mode .form-control::placeholder {
      color: #aaa;
    }
    
    body.dark-mode .list-group-item {
      background-color: #0f3460;
      border-color: #0a2848;
      color: #eee;
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
            <li><a class="nav-link text-white" href="javascript:void(0)" onclick="showBooks()">...</a></li>
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
            <button class="btn btn-sm btn-outline-secondary" id="btnModoOscuro" onclick="setLightMode('off')">
              <i class="bi bi-moon-stars"></i> Oscuro
            </button>
            <button class="btn btn-sm btn-outline-warning" id="btnModoClaro" onclick="setLightMode('on')">
              <i class="bi bi-sun"></i> Claro
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
        <hr>
        <div>
          <button class="btn btn-sm btn-danger w-100" onclick="eliminarTodosLosLibros()">
            <i class="bi bi-trash"></i> Eliminar todos los libros
          </button>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="col-lg-9 col-xl-10 offset-lg-3 offset-xl-2">
      <div id="main-content" class="container mt-4">
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
      this.contenido = contenido;
      this.portada = portada || "https://via.placeholder.com/200x250?text=Sin+Portada";
      this.categoria = categoria;
      this.fechaAgregado = new Date().toISOString();
    }
  }

  // Gestor de Biblioteca
  class BibliotecaVirtual {
    constructor() {
      this.libros = [];
      this.cargarDesdeLocalStorage();
      // Si no hay libros, inicializar con datos vacíos (sin ejemplos predefinidos)
      if (this.libros.length === 0) {
        this.libros = [];
        this.guardarEnLocalStorage();
      }
    }

    guardarEnLocalStorage() {
      localStorage.setItem('biblioteca_libros', JSON.stringify(this.libros));
    }

    cargarDesdeLocalStorage() {
      const guardados = localStorage.getItem('biblioteca_libros');
      if (guardados) {
        try {
          const parsed = JSON.parse(guardados);
          this.libros = parsed.map(l => Object.assign(new Libro(), l));
        } catch(e) {
          console.error("Error al cargar datos:", e);
          this.libros = [];
        }
      } else {
        this.libros = [];
        this.guardarEnLocalStorage();
      }
      this.actualizarEstadisticas();
    }

    agregarLibro(titulo, autor, descripcion, contenido, portada, categoria) {
      const nuevoId = this.libros.length > 0 ? Math.max(...this.libros.map(l => l.id)) + 1 : 1;
      const nuevoLibro = new Libro(nuevoId, titulo, autor, descripcion, contenido, portada, categoria);
      this.libros.push(nuevoLibro);
      this.guardarEnLocalStorage();
      this.actualizarEstadisticas();
      return nuevoLibro;
    }

    eliminarLibro(id) {
      this.libros = this.libros.filter(l => l.id !== id);
      this.guardarEnLocalStorage();
      this.actualizarEstadisticas();
    }

    eliminarTodos() {
      this.libros = [];
      this.guardarEnLocalStorage();
      this.actualizarEstadisticas();
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
        const totalContenido = this.libros.filter(l => l.contenido && l.contenido.length > 50).length;
        stats.innerHTML = `📚 ${this.libros.length} libros<br>📖 ${totalContenido} con contenido completo<br>⭐ ${this.libros.length * 5} lecturas estimadas`;
      }
    }
  }

  // Función para generar colores aleatorios pero consistentes por ID
  function getColorClass(id) {
    const colors = ['color-1', 'color-2', 'color-3', 'color-4', 'color-5', 'color-6', 'color-7', 'color-8'];
    return colors[id % colors.length];
  }

  // Inicializar biblioteca
  const biblioteca = new BibliotecaVirtual();
  let modoLuz = 'on'; // Por defecto modo claro

  // Función para eliminar todos los libros
  function eliminarTodosLosLibros() {
    if (confirm('⚠️ ¿Estás seguro de que quieres eliminar TODOS los libros? Esta acción no se puede deshacer.')) {
      biblioteca.eliminarTodos();
      showBooks();
      actualizarSidebarCategorias();
      alert('Todos los libros han sido eliminados.');
    }
  }

  // Funciones de UI
  function showBooks() {
    const container = document.getElementById('books-container');
    if (!container) return;
    
    const libros = biblioteca.obtenerTodos();
    
    if (libros.length === 0) {
      container.innerHTML = `
        <div class="text-center py-5">
          <i class="bi bi-emoji-frown" style="font-size: 4rem;"></i>
          <h3 class="mt-3">No hay libros en tu biblioteca</h3>
          <p class="text-muted">Haz clic en "Agregar" para añadir tu primer libro</p>
          <button class="btn btn-primary mt-2" onclick="showAddBookForm()">
            <i class="bi bi-plus-circle"></i> Agregar libro
          </button>
        </div>
      `;
      actualizarSidebarCategorias();
      return;
    }
    
    container.innerHTML = `
      <div class="row">
        ${libros.map(libro => `
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card book-card ${getColorClass(libro.id)} h-100" onclick="readBook(${libro.id})">
              <img src="${libro.portada}" class="card-img-top book-cover" alt="${libro.titulo}" onerror="this.src='https://via.placeholder.com/200x250?text=Portada+no+disponible'">
              <div class="card-body">
                <h5 class="card-title">${escapeHtml(libro.titulo)}</h5>
                <h6 class="card-subtitle mb-2">${escapeHtml(libro.autor)}</h6>
                <p class="card-text">${escapeHtml(libro.descripcion.substring(0, 100))}${libro.descripcion.length > 100 ? '...' : ''}</p>
                <span class="badge">${escapeHtml(libro.categoria)}</span>
              </div>
              <div class="card-footer bg-transparent d-flex justify-content-between">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); readBook(${libro.id})">
                  <i class="bi bi-book"></i> Leer
                </button>
                <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); eliminarLibro(${libro.id})">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
    
    actualizarSidebarCategorias();
  }

  function eliminarLibro(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este libro?')) {
      biblioteca.eliminarLibro(id);
      showBooks();
    }
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function actualizarSidebarCategorias() {
    const categorias = biblioteca.obtenerCategorias();
    const container = document.getElementById('categories-list');
    if (container) {
      if (categorias.length === 1 && categorias[0] === 'Todas') {
        container.innerHTML = '<small class="text-muted">No hay categorías</small>';
      } else {
        container.innerHTML = categorias.map(cat => `
          <a href="#" class="d-block text-decoration-none mb-1" onclick="filtrarPorCategoria('${cat}')">
            <i class="bi bi-tag"></i> ${cat}
          </a>
        `).join('');
      }
    }
  }

  function filtrarPorCategoria(categoria) {
    const libros = categoria === 'Todas' ? biblioteca.obtenerTodos() : biblioteca.obtenerPorCategoria(categoria);
    const container = document.getElementById('books-container');
    if (container) {
      if (libros.length === 0) {
        container.innerHTML = `
          <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem;"></i>
            <h4>No hay libros en "${categoria}"</h4>
            <button class="btn btn-primary mt-2" onclick="showBooks()">Ver todos</button>
          </div>
        `;
        return;
      }
      
      container.innerHTML = `
        <div class="row">
          ${libros.map(libro => `
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="card book-card ${getColorClass(libro.id)} h-100">
                <img src="${libro.portada}" class="card-img-top book-cover" alt="${libro.titulo}" onerror="this.src='https://via.placeholder.com/200x250?text=Portada+no+disponible'">
                <div class="card-body">
                  <h5 class="card-title">${escapeHtml(libro.titulo)}</h5>
                  <h6 class="card-subtitle mb-2">${escapeHtml(libro.autor)}</h6>
                  <p class="card-text">${escapeHtml(libro.descripcion.substring(0, 100))}${libro.descripcion.length > 100 ? '...' : ''}</p>
                  <span class="badge">${escapeHtml(libro.categoria)}</span>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-between">
                  <button class="btn btn-sm btn-primary" onclick="readBook(${libro.id})">
                    <i class="bi bi-book"></i> Leer
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="eliminarLibro(${libro.id})">
                    <i class="bi bi-trash"></i> Eliminar
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
                  <small class="text-muted">Deja vacío para usar imagen por defecto</small>
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
    
    // Remover event listener previo si existe y agregar nuevo
    const form = document.getElementById('addBookForm');
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    newForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const titulo = document.getElementById('titulo').value;
      const autor = document.getElementById('autor').value;
      const descripcion = document.getElementById('descripcion').value;
      const contenido = document.getElementById('contenido').value;
      const portada = document.getElementById('portada').value || "https://via.placeholder.com/200x250?text=Nuevo+Libro";
      const categoria = document.getElementById('categoria').value || "General";
      
      if (!titulo || !autor || !descripcion || !contenido) {
        alert('Por favor completa todos los campos obligatorios (*)');
        return;
      }
      
      biblioteca.agregarLibro(titulo, autor, descripcion, contenido, portada, categoria);
      alert(`✅ Libro "${titulo}" agregado correctamente`);
      showBooks();
    });
  }

  function readBook(id) {
    const libro = biblioteca.libros.find(l => l.id === id);
    if (!libro) return;
    
    document.getElementById('modalTitle').innerHTML = `<i class="bi bi-book"></i> ${escapeHtml(libro.titulo)} - ${escapeHtml(libro.autor)}`;
    document.getElementById('modalContent').innerHTML = `
      <div class="mb-3">
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> <strong>Descripción:</strong><br>
          ${escapeHtml(libro.descripcion)}
        </div>
        <div class="alert alert-secondary">
          <i class="bi bi-tag"></i> <strong>Categoría:</strong> ${escapeHtml(libro.categoria)}<br>
          <i class="bi bi-calendar"></i> <strong>Agregado:</strong> ${new Date(libro.fechaAgregado).toLocaleDateString()}
        </div>
        <hr>
        <h5><i class="bi bi-journal-bookmark-fill"></i> Contenido:</h5>
        <div class="p-3" style="white-space: pre-wrap;">${escapeHtml(libro.contenido || "Este libro aún no tiene contenido disponible.")}</div>
      </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('readModal'));
    modal.show();
  }

  function setLightMode(mode) {
    modoLuz = mode;
    const body = document.body;
    
    if (mode === 'on') {
      // Modo claro (luz encendida)
      body.classList.remove('dark-mode');
      localStorage.setItem('modoLuz', 'on');
    } else {
      // Modo oscuro (luz apagada)
      body.classList.add('dark-mode');
      localStorage.setItem('modoLuz', 'off');
    }
  }
  
  // Cargar el modo guardado al iniciar
  function cargarModoGuardado() {
    const modoGuardado = localStorage.getItem('modoLuz');
    if (modoGuardado === 'off') {
      setLightMode('off');
    } else {
      setLightMode('on');
    }
  }

  // Inicializar vista - SOLO UNA VEZ al cargar la página
  // Usamos DOMContentLoaded para asegurar que no se ejecute múltiples veces
  let inicializado = false;
  
 function inicializarApp() {
    if (inicializado) return;
    inicializado = true;
    
    cargarModoGuardado();
    actualizarSidebarCategorias(); // Se ejecuta una sola vez al inicio
    showBooks();                   // Se ejecuta una sola vez al inicio
}
  
  // Esperar a que el DOM esté completamente cargado
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarApp);
  } else {
    inicializarApp();
  }
</script>

</body>
</html>