<?php
/** @var \Laravel\Lumen\Routing\Router $router */

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


$router->get('/test', 'DummyController@test');
$router->post('/register','AccountsController@register');
$router->post('/login','AccountsController@login');


$router->get('/testxd',function () {
    return 'hey friend';
});


$router->get('/private', ['middleware' => 'auth',  "DummyController@test"]);