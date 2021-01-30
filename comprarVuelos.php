<?php
require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$cliente = new MongoDB\Client("mongodb://localhost:27017");

$colección = $cliente->adat_vuelos->vuelos_compra;

$parameters = file_get_contents("php://input");

$arrayInfo = array();

$datosViajero = array();

$plazasTotales = 0;
$plazasDisponibles = 0;
$precioBillete = 0;

if (isset($parameters)) {
  $jsonRecibido = json_decode($parameters, true);

  $codigoVuelo = $jsonRecibido["codigo"];
  $dniPagador = $jsonRecibido["dniPagador"];
  $tarjeta = $jsonRecibido["tarjeta"];


  $buscarVuelo = $colección->find(['codigo' => $codigoVuelo]);

  $arrayInfo["estado"]= true;

  foreach ($buscarVuelo as $entry) {
    $arrayInfo["codigo"] = $codigoVuelo;
    $arrayInfo["origen"] = $entry['origen'];
    $arrayInfo["destino"] = $entry['destino'];
    $arrayInfo["fecha"] = $entry['fecha'];
    $arrayInfo["hora"] = $entry['hora'];
    $plazasTotales = $entry['plazas_totales'];
    $plazasDisponibles = $entry['plazas_disponibles'];
    $precioBillete = $entry['precio'];
  }
  $arrayInfo["dniPagador"] = $dniPagador;
  $arrayInfo["tarjeta"] = $tarjeta;

  $n=9;

  //El siguiente metodo es para sacar el codigo de venta aleatorio
function getCodigoVenta($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}



  $arrayInfo["codigoVenta"] = getCodigoVenta($n);
  $datosBillete = array();


  foreach ($jsonRecibido["datosViajeros"] as $viajero) {
    $buscarVuelo = $colección->find(['codigo' => $codigoVuelo]);
    foreach ($buscarVuelo as $entry) {
      $plazasDisponibles = $entry['plazas_disponibles'];
    }

      $asiento = 1;

      $disponible = false;
      while ($disponible == false) {
          $buscarPlaza = $colección->find(['codigo' => $codigoVuelo, 'vendidos.asiento' => $asiento], ['_id' => 0, 'vendidos.asiento' => 1]);

        $plazasExistentes = array();
          foreach ($buscarPlaza as $key ) {
            foreach ($key['vendidos'] as $key2) {
              $plazasExistentes[] = $key2['asiento'];

            }
          }

          if (!in_array($asiento, $plazasExistentes)) {
            $disponible = true;
          } else {
            $asiento++;
          }
}

    $pasajero = array();

    $pasajero["asiento"] = $asiento;

    $pasajero["dni"] = $viajero["dni"];
    $pasajero["apellido"] = $viajero["apellido"];
    $pasajero["nombre"] = $viajero["nombre"];
    $pasajero["dniPagador"] = $dniPagador;
    $pasajero["tarjeta"] = $tarjeta;
    $pasajero["codigoVenta"] = $arrayInfo["codigoVenta"];
    $datosBillete[] = $pasajero;
    $resultados = $colección->updateOne(
       array("codigo" => $codigoVuelo),
       array('$push' => array("vendidos" => $pasajero))
     );
     $aux = $plazasDisponibles - 1;
     $resultados2 = $colección->updateOne(
        array("codigo" => $codigoVuelo),
        array('$set' => array("plazas_disponibles" => $aux))
      );

//Se crea un Json nuevo para enviar los datos solicitados en el Protocolo
    $pasajeroJsonEnviar = array();
$pasajeroJsonEnviar["asiento"] = $asiento;
$pasajeroJsonEnviar["dni"] = $viajero["dni"];
$pasajeroJsonEnviar["nombre"] = $viajero["nombre"];
$pasajeroJsonEnviar["apellido"] = $viajero["apellido"];
$pasajeroJsonEnviar["costeBillete"] = $precioBillete;


   }

   $arrayInfo["datosBillete"] = $pasajeroJsonEnviar;

$mensajeJSON = json_encode($arrayInfo, JSON_PRETTY_PRINT);
echo $mensajeJSON;

}else {
  $arrayInfo["estado"] = false;
  $arrayInfo["mensaje"] = "No se ha podido realizar la compra de su billete";
}

?>
