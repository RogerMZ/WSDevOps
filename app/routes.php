<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
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
        	almacent;';
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
  	$response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($jsonData));
    return $response;
  });

};
