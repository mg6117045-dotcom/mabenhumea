<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// ✅ DESPUÉS
include __DIR__ . '/db.php';

$pdo = conectarDB();

if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No has iniciado sesión']);
    exit;
}

$id_usuario = $_SESSION['id'];
$metodo = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

switch($metodo) {
    case 'GET': 
        $stmt = $pdo->prepare("SELECT * FROM libros WHERE id_usuario = ? ORDER BY id DESC");
        $stmt->execute([$id_usuario]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST': 
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        
        $stmt = $pdo->prepare("INSERT INTO libros (id_usuario, titulo, autor, descripcion, contenido, portada, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_usuario, 
            $datos['titulo'], 
            $datos['autor'], 
            $datos['descripcion'], 
            $datos['contenido'], 
            $datos['portada'], 
            $datos['categoria']
        ]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $id_libro = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM libros WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id_libro, $id_usuario]);
        echo json_encode(['success' => true]);
        break;
}