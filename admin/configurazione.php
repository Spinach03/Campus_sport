<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Configurazione";
$templateParams["titolo_pagina"] = "Configurazione";
$templateParams["nome"] = "configurazione.php";

require 'template/base.php';
?>
