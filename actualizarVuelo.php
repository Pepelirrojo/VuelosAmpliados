<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
require 'vendor/autoload.php';

$cliente = new MongoDB\Client("mongodb://localhost:27017");

$coleccion = $cliente->adat_vuelos->vuelos_compra;

$parameters = file_get_contents("php://input");

$arrayInfo = array();

if (isset($parameters)) {

$jsonRecibido = json_decode($parameters, true);

$codigoVuelo = $jsonRecibido['codigo'];
$dniViajero = $jsonRecibido['dniViajero'];
$codigoVenta = $jsonRecibido['codigoVenta'];
$dniNuevo = $jsonRecibido['dniNuevo'];
$apellido = $jsonRecibido['apellido'];
$nombre = $jsonRecibido['nombre'];


$resultados = $coleccion->updateOne(
array("codigo" => $codigoVuelo, "vendidos.dni" => $dniViajero, "vendidos.codigoVenta" => $codigoVenta),
array( '$set' => array(
            "vendidos.$.dni" => $dniNuevo,
            "vendidos.$.nombre" => $nombre,
            "vendidos.$.apellido" => $apellido
    )
)
);

if($resultados->getModifiedCount() == 1){
$arrayInfo['estado'] = true;
}else{
$arrayInfo['estado'] = false;
$arrayInfo['mensaje'] = "Los datos introducidos no son correctos";
}

$mensajeJSON = json_encode($arrayInfo, JSON_PRETTY_PRINT);
echo $mensajeJSON;
}

 ?>
