<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Le Mie Prenotazioni";
$templateParams["titolo_pagina"] = "Le Mie Prenotazioni";
$templateParams["nome"] = "le-mie-prenotazioni.php";

require 'template/base.php';
?>
