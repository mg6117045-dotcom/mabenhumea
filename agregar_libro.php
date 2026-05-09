<?php
require_once 'db.php';
$db = conectarDB();

$datos = [
    'titulo' => $_POST['titulo'],
    'isbn'   => $_POST['isbn'],
    'anio'   => $_POST['anio'],
    'autor'  => $_POST['id_autor'] // Este ID vendría de un <select> de autores
];

$sql = "INSERT INTO libros (titulo, isbn, anio_publicacion, id_autor) 
        VALUES (:titulo, :isbn, :anio, :autor)";
$db->prepare($sql)->execute($datos);
header("Location: dashboard.php?msj=LibroAgregado");
?>