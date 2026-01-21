<?php
require_once 'bootstrap.php';

// Se già loggato, redirect
if(isUserLoggedIn()){
    if(isAdmin()){
        header("Location: admin/index.php");
    } else {
        header("Location: utente/index.php");
    }
    exit;
}

// Gestione del login
if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $dbh->checkLogin($email, $password);

    if(count($user) > 0){
        registerLoggedUser($user[0]);
        
        if($user[0]['ruolo'] == 'admin'){
            header('Location: admin/index.php');
        } else {
            header('Location: utente/index.php');
        }
        exit;
    } else {
        $templateParams["errorelogin"] = "Email o password errati";
    }
}

$templateParams["titolo"] = "Campus Sports - Login";

require 'template/base-login.php';
?>
