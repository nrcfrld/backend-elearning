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



Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name

Route::get('email/resend', 'App\Htpp\Controllers\VerificationController@resend')->name('verification.resend');

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => ['api']], function (Router $api) {

    $api->get('/', 'App\Http\Controllers\CourseController@certificate');

    $api->post('/webhook', 'App\Http\Controllers\WebhookController@midtransHandler');


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
    * Categories
    */
    $api->group(['prefix' => 'categories'], function (Router $api) {
        $api->get('/', 'App\Http\Controllers\CategoryController@getAll');
        $api->get('/{uuid}', 'App\Http\Controllers\CategoryController@get');
    });

    /*
    * Courses
    */
    $api->group(['prefix' => 'courses'], function (Router $api) {
        $api->get('/', 'App\Http\Controllers\CourseController@getAll');
        $api->get('/{uuid}', 'App\Http\Controllers\CourseController@get');
    });


    /*
    * Chapters
    */
    $api->group(['prefix' => 'chapters'], function (Router $api) {
        $api->get('/', 'App\Http\Controllers\ChapterController@getAll');
        $api->get('/{uuid}', 'App\Http\Controllers\ChapterController@get');
    });

    /*
    * Lessons
    */
    $api->group(['prefix' => 'lessons'], function (Router $api) {
        $api->get('/', 'App\Http\Controllers\LessonController@getAll');
        $api->get('/{uuid}', 'App\Http\Controllers\LessonController@get');
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

        // * Users
        $api->put('/users/update-avatar/{user}', 'App\Http\Controllers\UserController@updateAvatar');
        $api->patch('/users/update-avatar/{user}', 'App\Http\Controllers\UserController@updateAvatar');
        $api->get('/users/{uuid}', 'App\Http\Controllers\UserController@get');

        // * Orders
        $api->post('/orders/enroll/{course}', 'App\Http\Controllers\OrderController@create');

        // * User Course
        $api->group(['prefix' => 'user-courses'], function (Router $api) {
            $api->get('/', 'App\Http\Controllers\UserCourseController@getAll');
            $api->get('/{uuid}', 'App\Http\Controllers\UserCourseController@get');
            $api->post('/', 'App\Http\Controllers\UserCourseController@post');
            $api->patch('/{uuid}', 'App\Http\Controllers\UserCourseController@patch');
            $api->delete('/{uuid}', 'App\Http\Controllers\UserCourseController@delete');
        });


        // Admin Only
        $api->group(['middleware' => 'check_role:admin'], function (Router $api) {
            /*
            * Users
            */
            $api->group(['prefix' => 'users'], function (Router $api) {
                $api->get('/', 'App\Http\Controllers\UserController@getAll');
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

            /*
            * Categories
            */
            $api->group(['prefix' => 'categories'], function (Router $api) {
                $api->post('/', 'App\Http\Controllers\CategoryController@post');
                $api->patch('/{uuid}', 'App\Http\Controllers\CategoryController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\CategoryController@delete');
            });

            /*
            * Courses
            */
            $api->group(['prefix' => 'courses'], function (Router $api) {
                $api->post('/', 'App\Http\Controllers\CourseController@post');
                $api->post('/upload-thumbnail/{uuid}', 'App\Http\Controllers\CourseController@uploadThumbnail');
                $api->patch('/{uuid}', 'App\Http\Controllers\CourseController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\CourseController@delete');
            });

            /*
            * Chapters
            */
            $api->group(['prefix' => 'chapters'], function (Router $api) {
                $api->post('/', 'App\Http\Controllers\ChapterController@post');
                $api->patch('/{uuid}', 'App\Http\Controllers\ChapterController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\ChapterController@delete');
            });

            /*
            * Lessons
            */
            $api->group(['prefix' => 'lessons'], function (Router $api) {
                $api->post('/', 'App\Http\Controllers\LessonController@post');
                $api->patch('/{uuid}', 'App\Http\Controllers\LessonController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\LessonController@delete');
            });

            /*
            * PaymentLogs
            */
            $api->group(['prefix' => 'payment-logs'], function (Router $api) {
                $api->get('/', 'App\Http\Controllers\PaymentLogController@getAll');
                $api->get('/{uuid}', 'App\Http\Controllers\PaymentLogController@get');
                $api->post('/', 'App\Http\Controllers\PaymentLogController@post');
                $api->patch('/{uuid}', 'App\Http\Controllers\PaymentLogController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\PaymentLogController@delete');
            });

            /*
            * Orders
            */
            $api->group(['prefix' => 'orders'], function (Router $api) {
                $api->get('/', 'App\Http\Controllers\OrderController@getAll');
                $api->get('/{uuid}', 'App\Http\Controllers\OrderController@get');
                $api->post('/', 'App\Http\Controllers\OrderController@post');
                $api->patch('/{uuid}', 'App\Http\Controllers\OrderController@patch');
                $api->delete('/{uuid}', 'App\Http\Controllers\OrderController@delete');
            });
        });
    });
});
