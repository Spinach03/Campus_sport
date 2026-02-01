<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Le mie prenotazioni";
$templateParams["titolo_pagina"] = "Le mie prenotazioni";
$templateParams["nome"] = "le-mie-prenotazioni.php";

require 'template/base.php';
?>