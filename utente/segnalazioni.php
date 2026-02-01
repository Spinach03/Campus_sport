<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Segnalazioni";
$templateParams["titolo_pagina"] = "Segnalazioni";
$templateParams["nome"] = "segnalazioni.php";

require 'template/base.php';
?>