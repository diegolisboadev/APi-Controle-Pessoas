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

use App\Events\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Rota Pai
$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Rotas da API Usuário
$router->group(['prefix' => 'usuarios'], function($router) {
    $router->post('registrar', 'Auth\AuthController@registrar');
    $router->post('login', 'Auth\AuthController@login');
});

// Rotas da API Controle Pessoas
$router->group(['middleware' => 'auth', 'prefix' => 'pessoas'], function($router) {
    $router->post('criar', 'ControlePessoasController@createPessoa');
    $router->get('pessoas', 'ControlePessoasController@pessoas');
    $router->get('pessoa/{id}', 'ControlePessoasController@pessoa');
    $router->put('pessoa/editar/{id}', 'ControlePessoasController@editarPessoa');
    $router->delete('pessoa/excluir/{id}', 'ControlePessoasController@excluirPessoa');
});

/*$router->get('teste', function() {
    $log = 'Teste';
    event(new App\Events\ControlePessoasEvent($log));
});*/
/*$router->get('teste', function() {
    $connection = new AMQPStreamConnection('rabbitmq_lumen', 5672, 'admin', '123456');
    $channel = $connection->channel();

    $channel->queue_declare('ola', false, false, false, false);

    $msg = new AMQPMessage('Olá mundo!!');
    $channel->basic_publish($msg, '', 'ola');

    echo " [x] Sent 'Olá mundo!'\n";

    $channel->close();
    $connection->close();

});*/
