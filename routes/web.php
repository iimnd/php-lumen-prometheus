<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/health', function (Request $req) {
    echo "OK";
    http_response_code(200);
  });
  $router->get('/metrics','PrometheusController@getProme');
  $router->get('/get_trx','PrometheusController@get_trx');
  $router->get('/get_report','PrometheusController@get_report');
  $router->get('/flush','PrometheusController@getFlush');
