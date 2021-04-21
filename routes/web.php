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

use Illuminate\Support\Facades\Auth;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', 'AuthController@login');
$router->post('/register', 'AuthController@register');
$router->get('/testToken', ['middleware' => 'auth', function (){
    return Auth::user();
}]);

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/testToken', function (){
        return Auth::user();
    });
    $router->get('/logout', 'AuthController@logout');
});

$router->group(['middleware' => 'auth', 'prefix'=> 'api/'], function () use ($router) {

    // Movie Routes
    $router->get('movies', 'MovieController@index');
    $router->get('movies/{id}', 'MovieController@show');
    $router->post('movies', 'MovieController@store');
    $router->put('movies/{id}', 'MovieController@update');
    $router->delete('movies/{id}', 'MovieController@destroy');

    // Actors Route
    $router->get('actors', 'ActorController@index');
    $router->get('actors/{id}', 'ActorController@show');
    $router->post('actors', 'ActorController@store');
    $router->delete('actors/{id}', 'ActorController@destroy');
});
