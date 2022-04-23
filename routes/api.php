<?php

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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function ($api) {

    $api->group(['middleware'=> ['api']], function ($api) {
        $api->post('login', 'AuthenticationController@login');
    });
    $api->group(['middleware'=> ['api','jwt.verify']], function ($api) {
        $api->post('logout', 'AuthenticationController@logout');
    });

    $api->group(['prefix'=>'loan','middleware'=> ['api','jwt.verify']], function ($api) {

        $api->post('apply', 'LoanController@applyLoan');
        $api->post('approve', 'LoanController@approveLoan');
        $api->post('pay-emi', 'LoanController@payEmi');
    });
});
