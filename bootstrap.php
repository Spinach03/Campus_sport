<?php
session_start();
require_once(__DIR__ . "/utils/functions.php");
require_once(__DIR__ . "/database/DatabaseHelper.php");
$dbh = new DatabaseHelper("localhost", "root", "", "campus_sports_arena", 3306);
?>