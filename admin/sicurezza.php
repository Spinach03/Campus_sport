<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Sicurezza";
$templateParams["titolo_pagina"] = "Sicurezza";
$templateParams["nome"] = "sicurezza.php";

require 'template/base.php';
?>
