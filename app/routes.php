<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Webscraping\WebscrapingAction;
require 'db.php';

return function (App $app) {

  //CORS
  $app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
  });

  $app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
  });

  // Routes
  $app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Hello world!');
    return $response;
  });

  $app->group('/users', function (Group $group) {
    $group->get('', ListUsersAction::class);
    $group->get('/{id}', ViewUserAction::class);
  });

  $app->get('/suma/{num1}/{num2}', function (Request $request, Response $response, $args) {
    $resultado = $args['num1'] + $args['num2'];
    $response->getBody()->write('Resultado: '.$resultado);
    return $response;
  });

  $app->post('/suma', function ($request, $response, $args) {
    $body = $request->getBody()->getContents();
    $elementos = json_decode($body,true);
    $num1 = $elementos["num1"];
    $num2 = $elementos["num2"];
    $resultado = $num1 + $num2;
    $response->getBody()->write('Resultado: '.$resultado);
    return $response;
});

  $app->get('/tabla', function (Request $request, Response $response, $args) {
    try
    {
      $conn = OpenConnection();
      // Armado de query
      $tsql = 'SELECT
        	Id,
        	UPC,
        	Concepto,
        	Cantidad,
        	Unidad,
        	Costo,
        	Precio_venta,
        	Can_min,
        	Provedor
        FROM
        	almacen;';
      // Ejecución de query
      $datos = mysqli_query($conn, $tsql);
      // Validamos la respuesta del query
      if ($datos == FALSE) {
        // Prepare response error
      	$response->withHeader('Content-Type', 'application/json');
        $errResponse = $response->withStatus(400);
      	$errResponse->getBody()->write(json_encode(array('Error:' => mysqli_error($conn))));
      	return $errResponse;
      }
      $datosCount = 0;
      $jsonData = array();
      while($row = mysqli_fetch_assoc($datos))
      {
        $jsonData[] = $row;
        $datosCount++;
      }
      mysqli_free_result($datos);
      mysqli_close($conn);
    }
    catch(Exception $e)
    {
      echo("Error!");
    }
    // Preparamos respuesta
  	$newResponse = $response->withHeader('Content-Type', 'application/json');
    $newResponse->getBody()->write(json_encode($jsonData));
    return $newResponse;
  });

  $app->post('/reglin', function ($request, $response, $args) {
    $body = $request->getBody()->getContents();
    $elementos = json_decode($body,true);
    //$num1 = $elementos["num1"];
    //$num2 = $elementos["num2"];
    //$resultado = $num1 + $num2;

    /*
     * Dada la ecuación y = mx + b
     *
     *     n(∑(xi)(yi))-∑(xi)*∑(yi)
     * m = ------------------------
     *     n∑(xi^2) - (∑(xi))^2
     *
     *      ∑yi - (m * ∑xi)
     * b = -----------------
     *             n
     *
     * y = mx + b
     *
     */
    /*
    $v_x = [1, 2, 3, 4, 5];
    $v_y = [5, 5, 5, 6.8, 9];
    $pos = 100;
     */
    $v_x = $elementos["v_x"];
    $v_y = $elementos["v_y"];
    $pos = $elementos["pos"];


    echo "Regresión lineal \n";

    $n = count($v_x);
    $x = 0;
    $y = 0;
    $xy = 0;
    $xx = 0;
    for($i = 0;$i < $n;$i++)
    {
      $x += $v_x[$i];
      $y += $v_y[$i];
      $xy += $v_x[$i] * $v_y[$i];
      $xx += $v_x[$i] ** 2;
    }

    $m = (($n * $xy) - ($x * $y)) / (($n * $xx) - ($x ** 2));

    $b = ($y - ($m * $x)) / $n;

    //echo "\nResultado: ".(($m * $pos) + $b);
    $response->getBody()->write('Resultado: '.(($m * $pos) + $b));
    return $response;
});

$app->post('/divisa', function ($request, $response, $args) {
  $body = $request->getBody()->getContents();
  $elementos = json_decode($body,true);
  $can = $elementos["cantidad"];
  $div = $elementos["divisa"];

  if ($can == '' || $div == '') {
    $response->withHeader('Content-Type', 'application/json');
    $errResponse = $response->withStatus(400);
    $errResponse->getBody()->write(json_encode(array('Error:' => 'Ingrese variables validas:// WARNING: ')));
    return $errResponse;
  }

  try
  {
    $conn = OpenConnection();
    // Armado de query
    $tsql = "SELECT tasa FROM Divisas where divisa = '$div'";
    // Ejecución de query
    $datos = mysqli_query($conn, $tsql);
    // Validamos la respuesta del query
    if ($datos == FALSE) {
      // Prepare response error
      $response->withHeader('Content-Type', 'application/json');
      $errResponse = $response->withStatus(400);
      $errResponse->getBody()->write(json_encode(array('Error:' => mysqli_error($conn))));
      return $errResponse;
    }
    $datosCount = 0;

    $tasa = 0;
    while($row = mysqli_fetch_array($datos))
    {
      $tasa = $row['tasa'];
    }
    mysqli_free_result($datos);
    mysqli_close($conn);
    $conv = $can * $tasa;
  }
  catch(Exception $e)
  {
    echo("Error!");
  }

  $response->withHeader('Content-Type', 'application/json');
  $response->getBody()->write(json_encode(array('conversion' => $conv)));
  return $response;
});

  $app->post('/convbase', function ($request, $response, $args) {
    $body = $request->getBody()->getContents();
    $elementos = json_decode($body,true);
    $valor = $elementos["valor"];
    $deBase = $elementos["deBase"];
    $aBase = $elementos["aBase"];
    $resp = 0;
    switch ($aBase) {
      case 'bin':
        $resp = "0b".decbin($valor);
        break;
      case 'oct':
        $resp = "0o".decoct($valor);
        break;
      case 'hex':
        $resp = "0x".dechex($valor);
        break;
      default:
        $resp = "Base invalida";
        break;
    }
    $response->getBody()->write(json_encode(array($deBase => $valor, $aBase => $resp)));
    return $response;
  });

  //webscrap
  $app->get('/webscrap/course/{search}', function (Request $request, Response $response, $args) {
        $webscrap = new WebscrapingAction();
        $list = $webscrap->getCourses($args["search"]);
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($list));
        return $response;
    });

};
