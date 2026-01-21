<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Profilo";
$templateParams["titolo_pagina"] = "Profilo";
$templateParams["nome"] = "profilo.php";

require 'template/base.php';
?>
