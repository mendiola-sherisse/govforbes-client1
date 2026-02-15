<?php
session_start();
if (!isset($_SESSION['CustomerID'])) {
    header('Location: login.php'); 
    exit;
}
