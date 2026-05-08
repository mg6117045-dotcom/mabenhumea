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
            $_SESSION['username'] = $usuario['email'];
            $_SESSION['id']       = $usuario['id'];

            setcookie("id_usuario", $usuario['id'], time() + (86400 * 30), "/");

            header("Location: dashboard.php");
            exit; // Solo una vez, nada después de aquí

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