<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function (){
	return view('map');
});

Route::get('requestTwitterPHPID/{loc}', function($loc){
    $settings = array(
        'oauth_access_token' => env('tw_oauth_access_token',''),
        'oauth_access_token_secret' => env('tw_oauth_access_token_secret',''),
        'consumer_key' => env('tw_consumer_key',''),
        'consumer_secret' => env('tw_consumer_secret','')
    );
    $url = 'https://api.twitter.com/1.1/trends/closest.json';
    $locArr = explode(",",$loc);
    //dd($locArr[0]);
    $getfield = '?lat='.$locArr[0].'&long='.$locArr[1];
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);

    $resultPHP = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();  

    echo $resultPHP;
});

Route::get('requestTwitterPHP/{id}', function($id){
    $settings = array(
        'oauth_access_token' => env('tw_oauth_access_token',''),
        'oauth_access_token_secret' => env('tw_oauth_access_token_secret',''),
        'consumer_key' => env('tw_consumer_key',''),
        'consumer_secret' => env('tw_consumer_secret','')
    );
    $url = 'https://api.twitter.com/1.1/trends/place.json';
    $getfield = '?id='.$id;
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);

    $resultPHP = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();  

    echo $resultPHP;
});