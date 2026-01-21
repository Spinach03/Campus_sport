<?php
function isUserLoggedIn(){
    return !empty($_SESSION['user_id']);
}

function isAdmin(){
    return isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'admin';
}

function registerLoggedUser($user){
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["nome"] = $user["nome"];
    $_SESSION["cognome"] = $user["cognome"];
    $_SESSION["ruolo"] = $user["ruolo"];
}

function logout(){
    session_unset();
    session_destroy();
}

function isActive($pagename){
    if(basename($_SERVER['PHP_SELF']) == $pagename){
        echo " class='active' ";
    }
}
?>
