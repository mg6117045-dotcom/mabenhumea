<?php
session_start();
require_once 'db.php';
$db = conectarDB();

$id_usuario = $_SESSION['id_usuario']; // El ID del que inició sesión
$id_libro = $_POST['id_libro'];
$fecha_devolucion = $_POST['fecha_devolucion']; // Ejemplo: '2026-05-15'

$sql = "INSERT INTO prestamos (id_usuario, id_libro, fecha_devolucion) 
        VALUES (:user, :libro, :fecha)";
        
$db->prepare($sql)->execute([
    'user'  => $id_usuario,
    'libro' => $id_libro,
    'fecha' => $fecha_devolucion
]);

header("Location: dashboard.php?msj=PrestamoExitoso");
?>