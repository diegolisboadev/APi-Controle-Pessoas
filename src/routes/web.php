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
