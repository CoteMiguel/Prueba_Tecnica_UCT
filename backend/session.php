<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    "id"     => $_SESSION['usuario']['id']     ?? null,
    "nombre" => $_SESSION['usuario']['nombre'] ?? null,
    "rol"    => $_SESSION['usuario']['rol']    ?? null,
    "permisos" => $_SESSION['usuario']['permisos'] ?? []
]);
