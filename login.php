<?php

require_once 'db.php';

$email = $_POST['email'];
$pwd   = $_POST['pwd'];

$db = conectarDB();

try {

    $sql   = "SELECT id, password, email FROM usuarios WHERE email = :email";
    $query = $db->prepare($sql);
    $query->execute(['email' => $email]);
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $verify = password_verify($pwd, $usuario['password']);

        if ($verify) {
            session_start();
      
$id_real = $usuario['id']; // Cambiado de id_usuario a id

$_SESSION['id_usuario'] = $id_real;
$cookie_name = "id_usuario";
$cookie_value = $id_real;
$expiry = time() + (86400 * 30); 
setcookie($cookie_name, $cookie_value, $expiry, "/");

header("Location: dashboard.php");
exit;



        } else {
            echo "La contraseña está mal...";
        }

    } else {
        echo "No se encontraron datos!";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>