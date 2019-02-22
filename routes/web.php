<?php

use Illuminate\Http\Request;

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

$router->post('login', [
    'as' => 'auth.login', 'uses' => 'AuthController@login',
]);

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('checklists', [
        'as' => 'checklists.index', 'uses' => 'ChecklistController@index',
    ]);
    $router->get('checklists/{id}', [
        'as' => 'checklists.get', 'uses' => 'ChecklistController@get',
    ]);
    $router->post('checklists', [
        'as' => 'checklists.save', 'uses' => 'ChecklistController@save',
    ]);
    $router->patch('checklists/{id}', [
        'as' => 'checklists.update', 'uses' => 'ChecklistController@update',
    ]);
    $router->post('checklists/{id}', [
        'as' => 'checklists.delete', 'uses' => 'ChecklistController@delete',
    ]);
});

$router->get('user', ['middleware' => 'auth', function (Request $request) {
    return new App\Http\Resources\User($request->user());
}]);
