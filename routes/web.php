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



$router->group(['prefix' => 'docente'], function () use ($router){
    $router->post('/agregar','Docente\DocenteController@agregarDocente');
    $router->get('/titulos/obtener','Docente\TituloController@obtenerTitulos');
    $router->post('/obtener','Docente\DocenteController@obtenerDocentes');
    $router->post('/obtener/todo','Docente\DocenteController@obtenerDocentesTodo');
    $router->put('/eliminar','Docente\DocenteController@eliminarDocente');
    $router->put('/actualizar','Docente\DocenteController@actualizarDocente');
});

$router->group(['prefix' => 'usuario'], function () use ($router){
    $router->post('/inicioSesion','UsuarioController@iniciarSesion');
    $router->post('/eliminar','UsuarioController@eliminarUsuario');
    $router->post('/crear','UsuarioController@crearUsuario');
    $router->post('/obtener','UsuarioController@obtenerUsuarios');
});

$router->group(['prefix' => 'titulo'], function () use ($router){
    $router->get('/obtener','Docente\TituloController@obtenerTitulos');
});