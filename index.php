<!DOCTYPE html>
<html>
	<head>
    	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.0/build/cssbase/cssbase-min.css">
    	<link href='http://fonts.googleapis.com/css?family=BenchNine' rel='stylesheet' type='text/css'>

    	<style type="text/css">
      		html { position:relative; height: 100%; width:100%; border:0px solid green; font-family: sans-serif; background: #f5f5f5; }
      		body { height:82%; margin: 0; padding: 10px; }
      		header { border:0px solid red;  }
      			header h1 { padding:0; margin:0; color: #eb7331; font-family: 'BenchNine', sans-serif; letter-spacing: 2px; line-height:90%;}
      			header hr { border-top: 12px solid black; margin:5px 0 0 0;}
      		
      		nav { border: 0px solid red; font-family: sans-serif, 'BenchNine'; letter-spacing:1px; font-weight:bold; font-size: 82%; margin: 20px 0;}
      			nav ul { padding:0; margin:0; }
      			nav ul li { display:inline-block; list-style:none; margin-right:10px;}

      		#main {display:block; position:relative; float:left; width:100%; height: 100%;}
      		
      		#map_canvas { height: 85%; width: 75%; margin: 5px 0 0 10px; float:left; position:relative;}
      		#rr { width: 20%; display:block; position:relative; float:left; margin: 5px 0 0 20px;}
      			#rr h3 { margin:0; border-top: 5px solid #555; border-bottom: 1px dashed #555; padding: 8px 0; font-size:72%; text-transform: uppercase; }
      			#rr ul { margin:0; padding:0;}
      			#rr ul li { list-style:none; font-size:72%; height:30px; border-bottom:1px dashed #d5d5d5; background:#fff; }
      			#rr ul li.last { border-bottom: none;}
      			#rr ul li.disabled { background: #f5f5f5; }
      			#rr ul li input {position:relative; margin: 8px 10px 0 5px;}
      			#rr ul li label { position:relative; display:block; margin: -16px 0 0 25px; cursor: auto; color:#767676;}

    	</style>
	</head>

	<body>
		<header>
			<h1>LA<br> BIKE MAP</h1>
			<hr />
		</header>

		<nav>
			<ul>
				<li>INCIDENTS</li>
				<li>ABOUT</li>
				<li>CONTACT US</li>
			</ul>
		</nav>
    	
    	<div id="main">
    		<div id="map_canvas"></div>

    		<div id="rr">
    			<h3>Incident Categories</h3>
    			<ul class="catList">
    				<li class="disabled"><input type="checkbox"/><label>All</label></li>
    				<li class="disabled"><input type="checkbox"/><label>Harrasement</label></li>
    				<li><input type="checkbox" checked="checked" /><label>Collision</label></li>
    				<li><input type="checkbox" checked="checked"/><label>Recovered Bike</label></li>
    				<li class="disabled"><input type="checkbox"/><label>Close Call</label></li>
    				<li class="disabled"><input type="checkbox"/><label>Fall</label></li>
    				<li class="disabled last"><input type="checkbox"/><label>Road Hazard</label></li>
    			</ul>

    			<div id="markerCount"></div>

                <div id="chart_div"></div>

    		</div>
    	</div>
    </body>

	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">

		/* Bikemap Javascript */
		var bikemapJS = (function() {

			// Google Map Variables			
			var map; 
			var API_KEY = 'AIzaSyCcdAQ2zf5JwAKPfP2u2x6ADtv6tXFTMII';
			var libraries = 'weather,places';
			var markerArr = [];
			var image = 'assets/marker.png';
			var jsonFile = 'bikemap.json';
			var markerCount;

			return {
				// The init function to get google Maps centered and zoom with our defaults
				init: function() {
					var mapOptions = {
						//Santa Monica, California
						center: new google.maps.LatLng(34.0194, -118.4903),
						zoom: 12,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

					var styles = [
					  {
					    stylers: [
					      { hue: "#00ffe6" },
					      { saturation: -100 }
					    ]
					  },{
					    featureType: "road",
					    elementType: "geometry",
					    stylers: [
					      { lightness: 100 },
					      { visibility: "simplified" }
					    ]
					  },{
					    featureType: "road",
					    elementType: "labels",
					    stylers: [
					      { visibility: "on" }
					    ]
					  }
					];
					map.setOptions({styles: styles});
					
					var bikeLayer = new google.maps.BicyclingLayer();
                    bikeLayer.setMap(map);

					bikemapJS.parse();
				},
				
                // Asynchronously loads the Google Maps API
				loadScript: function() {
					var script = document.createElement("script");
	  				script.type = "text/javascript";
	  				script.src = "http://maps.googleapis.com/maps/api/js?libraries=" + libraries + "&key=" + API_KEY + "&sensor=false&callback=bikemapJS.init";
	  				document.body.appendChild(script);
				},

				parse: function() {
					$.get(jsonFile, function(data) {
						bikemapJS.addMarkers(data);	
                    });
                },
                addMarkers: function(data) {
		          	//console.log(data);
		          	markerCount = data.length;
                	for(var i = data.length -1; i >= 0; i--) {
	                	var marker = new google.maps.Marker({
	     					position: new google.maps.LatLng(data[i].lat, data[i].long),
	      					map: map,
	      					icon: image,
	      					title: data[i].case_id + " / " + data[i].direction + " / " + parseInt(data[i].distance)
  						});
						
						/*
                		if(data[i].distance == 0) { 
                			radius = 1; 
                			fillColor = '#0000ff';
                		} else {
                			radius = parseInt(data[i].distance);                			
                			fillColor = '#ff0000';
                		}

					   var circleOptions = {
					      strokeColor: "#FF0000",
					      strokeOpacity: 0.8,
					      strokeWeight: 2,
					      fillColor: "#FF0000",
					      fillOpacity: 0.3,
					      map: map,
					      center: new google.maps.LatLng(data[i].lat, data[i].long),
					      radius: 30
					    };
					    cityCircle = new google.maps.Circle(circleOptions);
						*/
                	}	
                	console.log(markerCount);
                	$('#markerCount').html('Markers Displayed: '  + markerCount);

                }
                    
			}
		})();

		// Load the bikemap after DOM has loaded
		$(document).ready(function(){
			bikemapJS.loadScript();
		});

    </script>
</html>
