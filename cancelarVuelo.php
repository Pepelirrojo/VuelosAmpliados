<?php
require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$cliente = new MongoDB\Client("mongodb://localhost:27017");

$colección = $cliente->adat_vuelos->vuelos_compra;

$parameters = file_get_contents("php://input");

$asientos = array();

$arrayInfo = array();

if (isset($parameters)) {

$jsonRecibido = json_decode($parameters, true);

$codigoVuelo = $jsonRecibido['codigo'];
$dniViajero = $jsonRecibido['dni'];
$codigoVenta = $jsonRecibido['codigoVenta'];

$resultados = $colección->updateOne(
array("codigo" => $codigoVuelo),
array( '$pull' =>
    array(
        "vendidos" => array(
            "dni" => $dniViajero,
            "codigoVenta" => $codigoVenta
        )
    )
)
);

$buscarVuelo = $colección->find(['codigo' => $codigoVuelo]);
foreach ($buscarVuelo as $entry) {
  $plazasDisponibles = $entry['plazas_disponibles'];
}
$aux = $plazasDisponibles + 1;
$resultados2 = $colección->updateOne(
   array("codigo" => $codigoVuelo),
   array('$set' => array("plazas_disponibles" => $aux))
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
