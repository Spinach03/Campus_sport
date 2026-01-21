<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

$templateParams["titolo"] = "Campus Sports - Analytics";
$templateParams["titolo_pagina"] = "Analytics";
$templateParams["nome"] = "analytics.php";

require 'template/base.php';
?>
