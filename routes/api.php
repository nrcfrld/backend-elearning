<?php

use Dingo\Api\Routing\Router;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * Welcome route - link to any public API documentation here
 */

Route::get('/', function () {
    echo "Nothing";
});


Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name

Route::get('email/resend', 'App\Htpp\Controllers\VerificationController@resend')->name('verification.resend');

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => ['api']], function (Router $api) {
    /*
     * Authentication
     */
    $api->group(['prefix' => 'auth'], function (Router $api) {
        $api->post('/register', 'App\Http\Controllers\Auth\AuthController@register');

        $api->group(['prefix' => 'jwt'], function (Router $api) {
            $api->get('/token', 'App\Http\Controllers\Auth\AuthController@token');
        });
    });

    /*
     * Authenticated routes
     */
    $api->group(['middleware' => ['api.auth']], function (Router $api) {
        /*
         * Authentication
         */
        $api->group(['prefix' => 'auth'], function (Router $api) {
            $api->group(['prefix' => 'jwt'], function (Router $api) {
                $api->delete('/token', 'App\Http\Controllers\Auth\AuthController@logout');
                $api->get('/refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
            });

            $api->get('/me', 'App\Http\Controllers\Auth\AuthController@getUser');
        });

        $api->group(['middleware' => 'check_role:admin'], function(Router $api){
            /*
            * Users
            */
            $api->group(['prefix' => 'users'], function (Router $api) {
                $api->get('/', 'App\Http\Controllers\UserController@getAll');
                $api->get('/{uuid}', 'App\Http\Controllers\UserController@get');
                $api->post('/', 'App\Http\Controllers\UserController@post');
                $api->put('/{uuid}', 'App\Http\Controllers\UserController@put');
                $api->patch('/{uuid}', 'App\Http\Controllers\UserController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\UserController@delete');
            });

            /*
            * Roles
            */
            $api->group(['prefix' => 'roles'], function (Router $api) {
                $api->get('/', 'App\Http\Controllers\RoleController@getAll');
            });
        });
    });
});
