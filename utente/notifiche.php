<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Notifiche";
$templateParams["titolo_pagina"] = "Notifiche";
$templateParams["nome"] = "notifiche.php";

require 'template/base.php';
?>
