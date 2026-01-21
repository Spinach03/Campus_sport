<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Prenota Campo";
$templateParams["titolo_pagina"] = "Prenota Campo";
$templateParams["nome"] = "prenota-campo.php";

require 'template/base.php';
?>
