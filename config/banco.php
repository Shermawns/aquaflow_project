<?php
$host = 'localhost';
$user = 'root';
$db   = 'aquaflow';
$pass = '';

$banco = new mysqli($host, $user, $pass, $db);

if ($banco->connect_error) {
    die("Falha na conexão: " . $banco->connect_error);
}
