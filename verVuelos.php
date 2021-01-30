<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
require 'vendor/autoload.php';

$cliente = new MongoDB\Client("mongodb://localhost:27017");

$colección = $cliente->adat_vuelos->vuelos_compra;

$busqueda = array();
$contador = 0;
$arrayVuelos = array();
$arrayInfo = array();
$filtrado = false;

if(isset($_GET["fecha"]) && isset($_GET["origen"]) && isset($_GET["destino"])){
  $fechaS = $_GET["fecha"];
  $origenS = $_GET["origen"];
  $destinoS = $_GET["destino"];
  $busqueda["fecha"] = $fechaS;
  $busqueda["origen"] = $origenS;
  $busqueda["destino"] = $destinoS;

  $resultado = $colección->find(['$and'=>[ ['fecha' => $fechaS], ['origen' => $origenS], ['destino' => $destinoS]]]);
  $filtrado = true;
}else if(isset($_GET["fecha"]) && isset($_GET["origen"])){
  $fechaS = $_GET["fecha"];
  $origenS = $_GET["origen"];
  $busqueda["fecha"] = $fechaS;
  $busqueda["origen"] = $origenS;

  $resultado = $colección->find(['$and'=>[ ['fecha' => $fechaS], ['origen' => $origenS]]]);
  $filtrado = true;
}else{
  $resultado = $colección->find();
  $filtrado = false;
}
if (isset($resultado) && $resultado) {
    foreach ($resultado as $entry) {
      $contador++;
      $vuelo = array();
      $vuelo["codigo"] =$entry["codigo"];
      $vuelo["origen"] =$entry["origen"];
      $vuelo["destino"] =$entry["destino"];
      $vuelo["fecha"] =$entry["fecha"];
      $vuelo["hora"] =$entry["hora"];
      $vuelo["plazas_totales"] =$entry["plazas_totales"];
      $vuelo["plazas_disponibles"] =$entry["plazas_disponibles"];
      $vuelo["precio"] = $entry["precio"];
      $arrayVuelos[] = $vuelo;

    }
    $arrayInfo["estado"] = true;
    $arrayInfo["encontrados"]= $contador;

    if ($contador != 0) {
      if ($filtrado) {
        $arrayInfo["busqueda"] = $busqueda;
      }

    $arrayInfo["vuelos"] = $arrayVuelos;

    }
  } else {
  $arrayInfo["estado"] = false;
  $arrayInfo["mensaje"] = "No se ha podido realizar la consulta";

}

$mensajeJSON = json_encode($arrayInfo, JSON_PRETTY_PRINT);
echo $mensajeJSON;


?>
