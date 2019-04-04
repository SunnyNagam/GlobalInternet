<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Global Internet</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            #map {
                height: 80%;
                width: 80%;
            }
        </style>
    </head>
    <body>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet"/>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Global Internet
                </div>

                <div class="card thin">
                  <div class="card-body">
                    <h5 id= "locTitle" class="card-title">Select A Location To Get Started</h5>
                    <p id="locDesc" class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <ul class="list-group list-group-flush">
                        <a href="#" class="mt-3 btn btn-primary">Get Twitter Results</a>
                        <a href="#" class="mt-3 btn btn-primary">Get Flickr Images</a>
                    </ul>
                  </div>
                </div> 
            </div>
                <!-- TODO put key in .env -->
            <div id="map" class="mr-5"></div>
                <script async defer src="https://maps.googleapis.com/maps/api/js?key=PUT_UR_KEY_HERE&callback=initMap"></script>
            
        </div>
    </body>

    <script type="text/javascript">
        function initMap() {
          var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: {
              lat: 40.731,
              lng: -76.997
            }
          });

          var geocoder = new google.maps.Geocoder;
          var infowindow = new google.maps.InfoWindow;


          map.addListener('click', function(event) {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng();

            var latlng = new google.maps.LatLng(lat, lng);
            // This is making the Geocode request
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
              'latLng': latlng
            }, function(results, status) {
              if (status !== google.maps.GeocoderStatus.OK) {
                alert(status);
              }
              // This is checking to see if the Geoeode Status is OK before proceeding
              if (status == google.maps.GeocoderStatus.OK) {
                console.log(results);
                if (results[0]) {
                    // Set map marker
                  console.log(results[0].formatted_address);
                  address = (results[0].formatted_address);
                  map.setZoom(11);
                  var marker = new google.maps.Marker({
                    position: latlng,
                    map: map
                  });
                  infowindow.setContent(results[0].formatted_address);
                  infowindow.open(map, marker);

                  //Set location infromation on our site
                  document.getElementById("locTitle").innerHTML = address;
                  document.getElementById("locDesc").innerHTML = "Wow what a great place!"
                } else {
                  alert('No results found');
                }
              }
            });
          });
        }

        // TODO get tweets with https://api.twitter.com/1.1/search/tweets.json?geocode=45.51,-122.67,100km&rpp=50&q=%23olympics&callback=processResult
    </script>
</html>
    