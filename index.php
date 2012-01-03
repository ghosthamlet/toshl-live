<?php

/**
 * This code is an example of how to track live locations with google maps. For
 * a live example check http://toshl.com/live/
 *
 * For this code to work you must download GeoLiteCity.dat (binary) database from
 * http://www.maxmind.com/app/geolitecity because it is around 30MB and gets
 * updated every month. For any licensing restrictions please see
 * http://geolite.maxmind.com/download/geoip/database/LICENSE.txt
 *
 * @author Miha Hribar
 */

session_start();
// reset count -> see json.php for more info
$_SESSION['i'] = 0;

?>
<!DOCTYPE html>
<html>
  <head>
    <title>ToshL Live - Map of app syncs in real time</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <style type="text/css">
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        a { color: #ad242a; }
        img { border: 0; }

        #container {
            height:100%;
            height:100%;
            min-height:100%;
        }

        #header {
            width: 100%;
            height: 50px;
            background: #f0e8d5;
            overflow: hidden;
        }

        #content{
            bottom:0;
            margin-top:50px;
            position:absolute;
            top:0;
            width:100%;
        }

        #map_canvas {
            width: 100%;
            height: 100%;
            min-height: 100%;
        }

        #logo img { padding: 10px; float: left; }
        #header p { font-family: Helvetica, Arial; font-size: 15px; color: #666; float: left; padding-left: 10px; }
    </style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
    <script type="text/javascript">

    var map;            // holds map object
    var markers = [];   // list of markers
    var locations = []; // list of locations to show
    var iterator = 0;   // iterator over locations
    var zoomLevel = 4;  // initial zoom level
    var startPosition = new google.maps.LatLng(37.47, -122.26);

    // initialize map and position and start json requests
    function initialize() {
        var myOptions = {
            zoom: zoomLevel,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
        // center on start position
        map.setCenter(startPosition, zoomLevel);
        // start json requests
        getLocation();
    }

    // add new sync location
    // server returns a couple of them and they are slowly added to the map so the
    // user has time to follow them around the world (instead of just dropping all at once)
    function addLocation() {
        // get next from location array and show on map
        var pos = new google.maps.LatLng(locations[iterator][0], locations[iterator][1]);
        addMarker(pos, locations[iterator][2], true);
        iterator++;

        // call next iteration only if it already exist
        if (locations[iterator] != undefined) {
            setTimeout('addLocation()', 1500);
        }
    }

    // get new locations from server - don't try to do this from a different domain
    function getLocation() {
        $.ajax({
            url: 'json.php',
            success: function (data) {
                data = $.parseJSON(data);
                if ($.isArray(data) && data.length) {
                    for (loc in data) {
                        locations.push(data[loc]);
                    }
                    addLocation();
                }
                setTimeout('getLocation()', 3000);
            }
        });
    }

    // add marker on the map and pan to it
    function addMarker(position, text, center) {
        markers.push(new google.maps.Marker({
            position: position,
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            title: text
        }));
        // pan to marker
        if (center) {
            map.panTo(position);
        }
    }

    // add listener to initialize map
    google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=120196081433345";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <div id="container">
        <div id="header">
            <a href="http://toshl.com" id="logo"><img width="99" src="http://toshl.com/static/default/images/Default/logo_mini.png" alt="Toshl"></a>
            <p>Sync locations in <strong>real</strong> time. Make finance fun. <a href="http://toshl.com/expense-tracker-app/">Get ToshL for your smartphone now!</a> It's free.</p>
        </div>
        <div id="content">
            <div id="map_canvas"></div>
        </div>
    </div>
  </body>
</html>
