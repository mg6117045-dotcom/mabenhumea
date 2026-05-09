<?php
require_once 'db.php';
$db = conectarDB();

$datos = [
    'titulo' => $_POST['titulo'],
    'categoria'   => $_POST['categoria'],
    'anio'   => $_POST['anio'],
    'autor'  => $_POST['id_autor'] // Este ID vendría de un <select> de autores
];

$sql = "INSERT INTO libros (titulo, categoria, anio_publicacion, id_autor) 
        VALUES (:titulo, :categoria, :anio, :autor)";
$db->prepare($sql)->execute($datos);
header("Location: dashboard.php?msj=LibroAgregado");
?>