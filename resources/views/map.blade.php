<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Global Internet</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: transparent;/*#fff;*/
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                
            }
            html {
                background-image: linear-gradient(transparent, transparent 100vh, #88888844 140vh, black 200vh);
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
            .flickr_img {
                width: 500px;
                position: relative;
                margin-bottom: 10px;
                display: block;
            }
            .flickr_img > img{
                position: relative;
                top: 0;
                left: 0;
                width: 100%;
            }
            .flickr_img > .b_icon {
                position: absolute;
                z-index: 500;
                top: 12px;
                left: 12px;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background-attachment: local;
                background-size: contain;
                box-shadow: 0px 0px 8px 1px;
            }
            #loader {
                position: fixed;
                bottom: 1vmax;
                left: 1vmax;
                width: 10vmax;
                height: 10vmax;
                z-index: 1000;
            }
            #flickrResults, #twittrResults {
                min-height: 2000px;
                width: 102vw;
                margin-left: -1vw;

                background-color: transparent;
                float: left;
                display: inline-block;
                width: 50%;
            }
            .full-width {
                width: 100%;
            }
        </style>
    </head>
    <body>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet"/>
        <img id="loader" src="https://loading.io/spinners/typing/lg.-text-entering-comment-loader.gif">
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Global Internet
                </div>

                <div class="card thin">
                  <div class="card-body">
                    <h5 id= "locTitle" class="card-title">Select A Location To Get Started</h5>
                    <p id="locDesc" class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <ul class="list-group list-group-flush">
                        <a href="#" onclick="loadAllResults()" class="mt-3 btn btn-primary">Show Me Everything</a>
                        <a href="#" onclick="loadTwitterResults()" class="mt-3 btn btn-primary">Reload Twitter</a>
                        <a href="#" onclick="loadFlickrResults()" class="mt-3 btn btn-primary">Reload Flickr Images</a>
                    </ul>
                  </div>
                </div>
            </div>
            <div id="map" class="mr-5"></div>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GMAPS_API_KEY','null')}}&callback=initMap"></script>
        </div>

        <br>

        <div class="full-width" id="resultsContainer">
            <div id="twittrResults" style="width:500px;margin:auto;"></div>
            <div id="flickrResults" style="width:500px;margin:auto;"></div>
        </div>

    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
             $("#loader").hide();
        });
        // Global variables to talk between apis
        var geocoder;
        var infowindow;
        var map;
        var circle;
        var marker;
        var radius = 10000;
        var lat;
        var lng;
        function initMap() {
          map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: {
              lat: -6.2088,
              lng: 106.8456
            }
          });
          //alert("{{env('GMAPS_API_KEY','null')}}");

            geocoder = new google.maps.Geocoder;
            infowindow = new google.maps.InfoWindow;
            circle = null;
            marker = null;
            lat = null;
            lng = null;

          map.addListener('click', function(event) {
            lat = event.latLng.lat();
            lng = event.latLng.lng();

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
                    // Clear stuff and circles aldready on the map:
                    if (infowindow) {
                        infowindow.close();
                        infowindow = new google.maps.InfoWindow;
                        //setMapOnAll(null);
                    }
                    // Set map marker
                    console.log(results[0].formatted_address);
                    address = (results[0].formatted_address);
                    if(marker){
                        marker.setMap(null);
                    }
                    marker = new google.maps.Marker({
                        position: latlng,
                        map: map
                    });
                    if(circle){
                        circle.setMap(null);
                    }
                    circle = new google.maps.Circle({
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.25,
                        map: map,
                        center: {lat: lat, lng: lng},
                        radius: 10000
                    });
                    //infowindow.open(map, circle);
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                    map.setZoom(11);

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
        function loadAllResults() {
            // calls both asyncronously
            loadTwitterResults();
            loadFlickrResults();
        }
        // TODO get tweets with https://api.twitter.com/1.1/search/tweets.json?geocode=45.51,-122.67,100km&rpp=50&q=%23olympics&callback=processResult
        async function loadTwitterResults() {
            // if(!marker){
            //     return;
            // }
            $("#loader").show();
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var woeid = JSON.parse(this.responseText)[0]["woeid"];

                    var xmlhttp2 = new XMLHttpRequest();
                    xmlhttp2.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            $("#loader").hide();
                            //console.log(this.responseText);
                            var trends = JSON.parse(this.responseText)[0]["trends"];
                            var newhtml = "<div class='card' style='width: 60%; margin:auto;'>";
                            newhtml+= "<h5 class='card-title'>Trending Tweets</h5><h6 class='card-subtitle mb-2 text-muted'>in "+JSON.parse(this.responseText)[0]["locations"][0]["name"]+"</h6>";
                            newhtml += "<div class='list-group'>"
                            for(var i = 0; i < trends.length; i++){
                                var trend = trends[i];
                                //console.log(trend);
                                newhtml += "<a href='"+trend["url"]+"' target='_blank' class='list-group-item list-group-item-action'>"+trend["name"]+"</a>";
                            }
                            newhtml += "</div></div>"
                            document.getElementById("twittrResults").innerHTML = newhtml;

                        }
                    };
                    xmlhttp2.open("GET", "/requestTwitterPHP/"+woeid, true);
                    //xmlhttp2.open("GET", "/requestTwitterPHP", true);
                    xmlhttp2.send();
                }
            };
            xmlhttp.open("GET", "/requestTwitterPHPID/"+lat+","+lng, true);
            //xmlhttp.open("GET", "/requestTwitterPHP", true);
            xmlhttp.send();
        }

        async function loadFlickrResults() {

          //for now just +- 1
          var lat1 = lat - 1;
          var lng1 = lng - 1;
          var lat2 = lat + 1;
          var lng2 = lng + 1;

          $("#flickrResults").html("");

          recursiveLoad(1, 5, 1, {lat1: lat1, lat2: lat2, lng1: lng1, lng2: lng2});
        }

        function recursiveLoad(page, psize, num, info) {
            $("#loader").show();
            var yr = new XMLHttpRequest();
            yr.open("GET","/flickr_query/"+page+","+psize+","+info['lat1']+","+info['lat2']+","+info['lng1']+","+info['lng2'],true);
            yr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            // yr.send("p=" + page + "&psize=" + psize + "&lat1=" + info['lat1'] + "&lat2=" + info['lat2'] + "&lon1=" + info['lon1'] + "&lon2=" + info['lon2']);
            yr.send();
            yr.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    $("#loader").hide();
                    // $('#display').append(this.responseText);
                    var response = JSON.parse(this.responseText);
                    //console.log(response);
                    for (str in response){
                        let obj = JSON.parse(response[str]);
                        //console.log(obj);
                        // imgpg: $imgpg, imgsrc: $imgsrc, buddyicon: $buddyicon
                        let image = '<a class="flickr_img"><img src="' + obj.imgsrc + '" /></a>';
                        image = $(image).attr('href',obj.imgpg);
                        //let container = $(image).wrap('');
                        let set = $(image).append('<div class="b_icon" style="background-image:url(\'' + obj.buddyicon + '\')">');
                        $("#flickrResults").append(set);
                    }
                    // fixBackground();
                    if (page < num)
                    recursiveLoad(page+1, psize, num, info);
                    // else
                    //   $("#loader").hide();
                }
            };
        }


    </script>
</html>
