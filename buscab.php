<?php
define('BASE_PATH', __DIR__);
require_once 'controller/buscab_controller.php';

$controller = new BuscabController();
$controller->handleRequest();
?>