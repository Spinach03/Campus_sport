<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Campi Disponibili";
$templateParams["titolo_pagina"] = "Campi Disponibili";
$templateParams["nome"] = "campi-disponibili.php";

require 'template/base.php';
?>
