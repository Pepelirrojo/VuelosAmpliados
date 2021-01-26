<?php
require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		require 'verVuelos.php';
		break;
	case 'POST':
		require 'comprarVuelos.php';
		break;
	case 'DELETE':
		require 'cancelarVuelo.php';
		break;
	case 'PUT':
		require 'actualizarVuelo.php';
		break;
	default:
		break;
}
?>
