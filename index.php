<!DOCTYPE html>
<html>
	<head>
    	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.0/build/cssbase/cssbase-min.css">
    	<link href='http://fonts.googleapis.com/css?family=BenchNine' rel='stylesheet' type='text/css'>
    	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" />

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
      		#markerCount { margin-top: 20px; }
      		#map_canvas { height: 85%; width: 75%; margin: 5px 0 0 10px; float:left; position:relative;}
      		#rr { width: 20%; display:block; position:relative; float:left; margin: 5px 0 0 20px;}
      			#rr h3 { margin:0; border-top: 5px solid #555; border-bottom: 1px dashed #555; padding: 8px 0; font-size:72%; text-transform: uppercase; }
      			#rr ul { margin:0; padding:0;}
      			#rr ul li { list-style:none; font-size:72%; height:30px; border-bottom:1px dashed #d5d5d5; background:#fff; }
      			#rr ul li.last { border-bottom: none;}
      			#rr ul li.disabled { background: #f5f5f5; }
      			#rr ul li input {position:relative; margin: 8px 5px 0 5px; cursor:pointer;}
      			#rr ul li label { position:relative; display:block; margin: -12px 0 0 45px; cursor: auto; color:#767676;}
		      		#rr .colourBlock { height: 10px; width: 10px; display:block; position:relative; border:1px solid #ccc; border-radius: 4px;margin: -16px 0 0 25px; }
		      		#rr .labelRed {  background:red; }
					#rr .labelOrange { background:orange; }
					#rr .labelYellow { background:yellow; }
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
    				<li>
    					<input type="checkbox" name='fatal' checked="checked"/>
    					<div class="labelRed colourBlock"></div>
    					<label>Fatality</label>
    				</li>
    				<li>
    					<input type="checkbox" name='severe'/>
    					<div class="labelOrange colourBlock"></div>
    					<label>Severe Injury</label>
    				</li>
    				<li>
    					<input type="checkbox" name='minor'/>
    					<div class="labelOrange colourBlock"></div>
    					<label>Minor Injury</label>
    				</li>
    				<li>
    					<input type="checkbox" name='pdo' />
    					<div class="labelYellow colourBlock"></div>
    					<label>Property Damage</label>
    				</li>
    			</ul>

    			<div id="slider-range"></div>

    			<div id="markerCount"></div>

                <div id="chart_div"></div>

    		</div>
    	</div>
    </body>

	<script async type="text/javascript" src="jquery.js"></script>
    <script async type="text/javascript" src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
    <script async type="text/javascript" src="https://www.google.com/jsapi"></script>
    
	<script async type="text/javascript">

		/* Bikemap Javascript */
		var bikemapJS = (function() {

			// Google Map Variables			
			var map; 
			var API_KEY = 'AIzaSyCcdAQ2zf5JwAKPfP2u2x6ADtv6tXFTMII';
			var libraries = 'weather,places';
			var image = 'assets/marker_fatal.png';
			var markersArray = [];
      var markerCount;

			return {

				// Asynchronously loads the Google Maps API
				loadScript: function() {
					var script = document.createElement("script");
	  				script.type = "text/javascript";
	  				script.src = "http://maps.googleapis.com/maps/api/js?libraries=" + libraries + "&key=" + API_KEY + "&sensor=false&callback=bikemapJS.init";
	  				document.body.appendChild(script);
				},

				// The init function to get google Maps centered and zoom with our defaults
				init: function() {
					var mapOptions = {
						//Santa Monica, California
						center: new google.maps.LatLng(34.0194, -118.3903),
						zoom: 11,
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
                    
          bikemapJS.addListeners();
					bikemapJS.parseNav();
				},

        // Create our event listeners
				addListeners: function() {
					$('.catList li input').click(function() {
						bikemapJS.parseNav();
					});
				},

				// Cycle through Catrgory checkboxes, and make a request based on the selection
        parseNav: function() {
					var url = 'query.php';
					var catStr = '';
					// Look for each checked Catergory and create a query string
          $('.catList li input').each(function() {
            if( $(this).is(':checked') ) {
              catStr += $(this).attr('name') + ',';
            }
    			});
          // Remove any extra commas of they exist
					if(catStr[catStr.length -1] == ',') { 
						catStr = catStr.substr(0,catStr.length -1);
					}

          bikemapJS.clearAllMarkers();

          // Do an ajax request for data if any checkbox are selected
          if( typeof catStr != 'undefined' && catStr != '') {
            url += '?cat=' + catStr;
            $.get(url, function(data) {
              bikemapJS.addMarkers(data);	
            });
          }
        },

        // Clears all map markers
        clearAllMarkers: function() {
          markerCount = 0;
          for(i in markersArray) {
            markersArray[i].setMap(null);
          }
          markersArray = [];
          $('#markerCount').html('Markers Displayed: '  + markerCount);
        },
                
        // Upon recieving data, we places markers on the map
        addMarkers: function(data) {
          data = JSON.parse(data);
          // Go through each of the available categories in json data
          for(var cat in data) {
            var markerIcon = 'assets/marker_' + cat + '.png';       
            markerCount += data[cat].length;
                 
            // create a marker for each incident
            for(var i = data[cat].length -1; i >= 0; i--) {
        		  var marker = new google.maps.Marker({
                position: new google.maps.LatLng(data[cat][i].lat, data[cat][i].long),
	 				      map: map,
                icon: markerIcon,
                title: data[cat][i].case_id
              });
              markersArray.push(marker);
              bikemapJS.addOverlay(marker, data[cat][i]);
            }
          }
          $('#markerCount').html('Markers Displayed: '  + markerCount);
          bikemapJS.updateSlider();
        },
                
        addOverlay: function(marker, data){
          google.maps.event.addListener(marker, 'click', function() {
            var url = 'query2.php?id=' + data.case_id; 
            $.get(url, function(payload){
              data = JSON.parse(payload)[0];
              console.log(data);
              var cs = '';
              cs += '<div>';
              cs += '<h3>' + data.primary_rd + ' / ' + data.secondary_rd + '</h3>';  
              cs += '</div>';
              var infowindow = new google.maps.InfoWindow({
                content: cs
              });
              infowindow.open(map, marker);
            }); 
          });
        },

        updateSlider: function() {
          $( "#slider-range" ).slider({
            range: true,
            min: 0,
      			max: 500,
      			values: [ 75, 300 ],
      			slide: function( event, ui ) {
        		  $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      			}
    			});
        }
			}
		})();

		// Load the bikemap after DOM has loaded
		$(document).ready(function(){
			bikemapJS.loadScript();
		});

    </script>
</html>
