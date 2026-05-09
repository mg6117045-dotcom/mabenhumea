<?php
require_once 'db.php';
$db = conectarDB();
$nombre = $_POST['nombre_autor'];

$sql = "INSERT INTO autores (nombre) VALUES (:nombre)";
$db->prepare($sql)->execute(['nombre' => $nombre]);
header("Location: dashboard.php?msj=AutorAgregado");
?>