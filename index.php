<?php
require_once 'bootstrap.php';

if(isUserLoggedIn()){
    if(isAdmin()){
        header("Location: admin/index.php");
    } else {
        header("Location: utente/index.php");
    }
} else {
    header("Location: login.php");
}
exit;
?>
