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
    // Funciones con token
    $router->post('/agregar','Docente\DocenteController@agregarDocente');
    $router->get('/obtener/todo','Docente\DocenteController@obtenerDocentesTodo');
    $router->put('/eliminar','Docente\DocenteController@eliminarDocente');
    $router->put('/actualizar','Docente\DocenteController@actualizarDocente');
    // Funciones sin token
    $router->post('/obtener','Docente\DocenteController@obtenerDocentes');
    
    $router->group(['prefix' => 'titulo'], function () use ($router){
        $router->get('/obtener','Docente\TituloController@obtenerTitulos');
    });
    
    $router->group(['prefix' => 'papers'], function () use ($router){
        $router->get('/obtener/{idDocente}','Docente\PapersController@obtenerPapers');
        $router->post('/ingresar','Docente\PapersController@ingresarPaper');
    });
});

$router->group(['prefix' => 'usuario'], function () use ($router){
    // Funciones con token
    $router->post('/inicioSesion','UsuarioController@iniciarSesion');
    $router->put('/eliminar','UsuarioController@eliminarUsuario');
    $router->post('/crear','UsuarioController@crearUsuario');
    $router->post('/obtener','UsuarioController@obtenerUsuarios');
    //Funciones sin token
});

$router->group(['prefix' => 'noticia'], function () use ($router){
    $router->get('/obtener','NoticiaController@obtenerNoticias');
    $router->post('/agregar','NoticiaController@ingresarNoticia');
    $router->put('/eliminar','NoticiaController@eliminarNoticia');
    $router->put('/actualizar','NoticiaController@actualizarNoticia');

});

$router->group(['prefix' => 'evento'], function () use ($router){
    $router->get('/obtener','EventoController@obtenerEventos');
    $router->post('/agregar','EventoController@ingresarEvento');
    $router->put('/eliminar','EventoController@eliminarEvento');
    $router->put('/actualizar','EventoController@actualizarEvento');
});

$router->group(['prefix' => 'estudiante'], function () use ($router){
    $router->post('/agregar','Estudiante\EstudianteController@agregarEstudiante');
    $router->get('/obtener','Estudiante\EstudianteController@obtenerEstudiantes');
    $router->put('/eliminar','Estudiante\EstudianteController@eliminarEstudiante');
    $router->put('/actualizar','Estudiante\EstudianteController@actualizarEstudiante');

    $router->group(['prefix' => 'tesis'], function () use ($router){    
        $router->get('/obtener/todo','Estudiante\TesisController@obtenerTesises');
        $router->get('/obtener/contenido/{idTesis)','Estudiante\TesisController@obtenerContenido');
        $router->post('/ingresar','Estudiante\TesisController@ingresarTesis');
        $router->put('/eliminar','Estudiante\TesisController@eliminarTesis');
    });
    
    $router->group(['prefix' => 'nivelAcademico'], function () use ($router){    
        $router->get('/obtener','Estudiante\NivelAcademicoController@obtenerNivelAcademico');
        $router->post('/ingresar','Estudiante\NivelAcademicoController@ingresarNivelAcademico');
    });
});

$router->group(['prefix' => 'solicitud'], function () use ($router){
    $router->get('/obtener','SolicitudController@obtenerSolicitudes');
    $router->post('/enviar','SolicitudController@enviarSolicitud');
});

$router->group(['prefix' => 'pensum'], function () use ($router){
    $router->get('/obtener/{opcion}','PensumController@obtenerPensums');
    $router->post('/ingresar','PensumController@ingresarPensum');
    $router->put('/eliminar','PensumController@eliminarPensum');
    $router->put('/actualizar','PensumController@actualizarPensum');
    $router->put('/activar','PensumController@activarPensum');
    $router->put('/desactivar','PensumController@desactivarPensum');
});

$router->group(['prefix' => 'contacto'], function () use ($router){
    $router->get('/obtener','ContactoController@obtenerContactos');
    $router->post('/ingresar','ContactoController@ingresarContacto');
    $router->put('/eliminar','ContactoController@eliminarContacto');
    $router->put('/actualizar','ContactoController@actualizarContacto');
});