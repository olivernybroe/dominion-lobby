<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'api'], function (Router $api) {

    $api->group(['middleware' => 'auth:api'], function (Router $api) {
        $api->get("works", function () {
            return "works";
        });

        $api->group(['prefix' => 'users'], function (Router $api) {
            $api->get('', "UserController@all");
            $api->post('', "UserController@create");

            $api->group(['prefix' => '{user}'], function (Router $api) {
                $api->get('', "UserController@get");
                $api->delete('', "UserController@delete");
                $api->patch('', "UserController@update");
            });

        });

        $api->group(['prefix' => 'lobbies'], function (Router $api) {
            $api->post('', "LobbyController@add");
            $api->get('', 'LobbyController@all');

            $api->group(['prefix' => '{lobby}'], function (Router $api) {
                $api->get('', 'LobbyController@get');
                $api->delete('', "LobbyController@delete");


                $api->group(['prefix' => 'games'], function (Router $api) {
                    $api->group(['prefix' => 'previous'], function (Router $api) {
                        $api->get('', "GameController@all");

                        $api->group(['prefix' => '{game}'], function (Router $api) {
                            $api->get('', "GameController@get");
                            $api->delete('', "GameController@delete")->middleware('scope:admin');
                        });
                    });

                    $api->group(['prefix' => 'current'], function (Router $api) {
                        $api->post('', "GameController@create");
                        $api->get('', "GameController@getCurrentGame");
                        $api->delete('', "GameController@EndCurrentGame")->middleware('scope:admin');
                    });
                });
                $api->group(['prefix' => 'users'], function (Router $api) {
                    $api->group(['prefix' => 'players'], function (Router $api) {
                        $api->get('', "LobbyController@allPlayers");
                        $api->post("", "LobbyController@joinAsPlayer");
                        $api->delete("", "LobbyController@leaveAsPlayer")->middleware('scope:admin');
                    });
                    $api->group(['prefix' => 'spectators'], function (Router $api) {
                        $api->get('', "LobbyController@allSpectators");
                        $api->post("", "LobbyController@joinAsSpectator");
                        $api->delete("", "LobbyController@leaveAsSpectator")->middleware('scope:admin');
                    });
                    $api->group(['prefix' => 'current'], function (Router $api) {
                        $api->get('', "LobbyController@getCurrentUser");
                        $api->delete('', "LobbyController@leaveAsCurrentUser");
                        $api->patch('', "LobbyController@ready");
                    });
                });
            });
        });
    });
});

