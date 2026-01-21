<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Recensioni";
$templateParams["titolo_pagina"] = "Recensioni";
$templateParams["nome"] = "recensioni.php";

require 'template/base.php';
?>
