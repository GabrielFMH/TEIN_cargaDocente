<?php
define('BASE_PATH', __DIR__);
require_once 'controller/carga_controller.php';

$controller = new CargaController();
$controller->handleRequest();
?>