<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Comunicazioni";
$templateParams["titolo_pagina"] = "Comunicazioni";
$templateParams["nome"] = "comunicazioni.php";

require 'template/base.php';
?>
