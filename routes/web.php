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

Route::get('flickr_query/{data}', function($data){

// dd($data);
$data_array = explode(",", $data);
  $page=$data_array[0];
  $psize=$data_array[1];
  $lat1 = $data_array[2];
  $lat2 = $data_array[3];
  $lon1 = $data_array[4];
  $lon2 = $data_array[5];
//  dd($data);
  //exit;
  // $type = $_REQUEST['type'];
  // $page = (empty($_REQUEST['p']))? 1 : $_REQUEST['p'];
  // $psize = (empty($_REQUEST['psize']))? 20 : $_REQUEST['psize'];

  $flickr_key = env('FLICKR_API_KEY', '');
  $params = array( 'api_key'        => $flickr_key,
                   'method'         => 'flickr.photos.search',
                   'bbox'           => "$lon1,$lat1,$lon2,$lat2", // -114,50,-113,51
                   'extras'         => 'geo',
                   'has_geo'        => '1',
                   'per_page'       => $psize,
                   'page'           => $page,
                   'format'         => 'json',
                   'nojsoncallback' => '1');
  $encoded_params = array();
  foreach ($params as $k => $v){
    $encoded_params[] = urlencode($k).'='.urlencode($v);
  }

  $url = "https://api.flickr.com/services/rest/?" . implode('&', $encoded_params);

  $rsp = file_get_contents($url);

  $rsp = str_replace( 'jsonFlickrApi(', '', $rsp );
  $rsp = substr( $rsp, 0, strlen( $rsp ) );
  $rsp2 = json_decode($rsp, true);
  // dd($rsp2);
  //
  // echo("help");
  // exit;
  $photos = $rsp2['photos']['photo'];
  // echo("help");
  // exit;
  $retArr = array();
  foreach ($photos as $key => $value) {
    $imgsrc = 'https://farm' . $value["farm"] . '.staticflickr.com/'.
    $value["server"] . '/' . $value["id"] . '_' . $value["secret"] . '.jpg';
    $imgpg = 'https://www.flickr.com/photos/' . $value['owner'] . '/' . $value["id"];
    //if ($type!="JSON") {
      $encoded_params = $arrayName = array();
      $params = array( 'api_key' => $flickr_key,
                       'method'  => 'flickr.people.getInfo',
                       'format'  => 'json',
                       'user_id' => $value['owner']);
      foreach ($params as $k => $v){
       $encoded_params[] = urlencode($k).'='.urlencode($v);
      }
      try {
        $user = file_get_contents("https://api.flickr.com/services/rest/?" . implode('&', $encoded_params));
        $user = substr($user, 14, strlen( $user )-15);
        $person = json_decode($user, true)['person'];
        $buddyicon = 'http://farm' . $person['iconfarm'] . '.staticflickr.com/' . $person['iconserver'] . '/buddyicons/' . $value['owner'] . '.jpg';
        if( $person['iconfarm'] < 1 ) $buddyicon = 'https://www.flickr.com/images/buddyicon.gif';
      }catch(Exception $e) {
        $buddyicon = 'https://www.flickr.com/images/buddyicon.gif';
      }


      // $jsonobj = imgpg: $imgpg, imgsrc: $imgsrc, buddyicon: $buddyicon};

      array_push($retArr, "{\"imgpg\": \"$imgpg\", \"imgsrc\": \"$imgsrc\", \"buddyicon\": \"$buddyicon\"}");

    //}
  }
  return json_encode($retArr);
  //exit;

  // if ($type=="JSON") {
  //   echo '<pre>';
  //   echo "{" . json_encode($rsp2['photos']['photo']) . "}\n";
  //   echo '{"photos":' . "\n[";
  //   foreach ($rsp2['photos']['photo'] as $value) {
  //     echo '{ '  . "\n";
  //     foreach ($value as $key => $var) {
  //       echo ($key == 'id')? "" : ",\n";
  //       echo "\t" . '"' . $key . '": "' . $var . '"';
  //     }
  //     echo "\n" . '},';
  //   }
  //   echo "]}";
  //   echo '</pre>';
  // }
});
