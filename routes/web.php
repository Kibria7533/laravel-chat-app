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
    return "wellcome";
});
$router->get('test', function () {
    event(new App\Events\StatusLiked('Someone'));
    return "Event has been sent!";
});

$router->get('/contacts', ["as" => "contacts", "uses" => "ContactController@getList"]);
$router->get('/conversations', ["as" => "conversations", "uses" => "ConversationController@getList"]);
$router->get('/messeges/{initId}/{recId}', ["as" => "messeges", "uses" => "MessegeController@getList"]);
$router->post('/contact', ["as" => "contact", "uses" => "ContactController@store"]);
$router->post('/conversation', ["as" => "conversation", "uses" => "ConversationController@store"]);
$router->post('/messege', ["as" => "messege", "uses" => "MessegeController@store"]);
$router->post('/group', ["as" => "group", "uses" => "GroupMemberController@store"]);
$router->get('/gmesseges/{gid}/{gname}', ["as" => "gmesseges", "uses" => "GroupMemberController@getMesseges"]);
