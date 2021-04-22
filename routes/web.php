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
    $router->post('movies/{id}', 'MovieController@update');
    $router->delete('movies/{id}', 'MovieController@destroy');

    // Movie Trailers
    $router->post('movies/{id}/trailers', 'MovieController@addTrailer');
    $router->delete('movies/trailers/{id}', 'MovieController@removeTrailer');

    // Movie Actors
    $router->patch('movies/{id}/actors/{actor_id}', 'MovieController@addActor');
    $router->delete('movies/{id}/actors/{actor_id}', 'MovieController@removeActor');

    // Movie Directors
    $router->patch('movies/{id}/directors/{director_id}', 'MovieController@addDirector');
    $router->delete('movies/{id}/directors/{director_id}', 'MovieController@removeDirector');

    // Actors Route
    $router->get('actors', 'ActorController@index');
    $router->get('actors/{id}', 'ActorController@show');
    $router->post('actors', 'ActorController@store');
    $router->delete('actors/{id}', 'ActorController@destroy');

    // Director Route
    $router->get('directors', 'DirectorController@index');
    $router->get('directors/{id}', 'DirectorController@show');
    $router->post('directors', 'DirectorController@store');
    $router->delete('directors/{id}', 'DirectorController@destroy');

    // Genre Route
    $router->get('genres', 'GenreController@index');
    $router->get('genres/{id}', 'GenreController@show');
    $router->post('genres', 'GenreController@store');
    $router->delete('genres/{id}', 'GenreController@destroy');

    // Rating Route
    $router->get('ratings', 'RatingController@index');
    $router->get('ratings/{id}', 'RatingController@show');
    $router->post('ratings', 'RatingController@store');
    $router->delete('ratings/{id}', 'RatingController@destroy');
});
