
<!DOCTYPE html>

<html>
  <head>
	<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script>

        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 11,
            center: { lat: 41.0053702, lng: 28.6825452 },
            });

			let portfolio = @json($portfolio);
            let marker;
			//If The Portfolio Has Lat/Long Values In DB
			//Create Marker and Set to Map
			//Change Center Of Map
			if(portfolio.latitude != null){
				var portfolioLatlng = new google.maps.LatLng(portfolio.latitude,portfolio.longitude);
				var modalLatEl = $('#modal-lat');
				var modalLongEl = $('#modal-long');
				modalLatEl.val(portfolio.latitude);
				modalLongEl.val(portfolio.longitude);

				marker = new google.maps.Marker({
					position: portfolioLatlng,
				})
				marker.setMap(map);
				map.setCenter(new google.maps.LatLng(portfolio.latitude,portfolio.longitude));
			}
			//When The Map is clicked
			//Delete If There is another marker and set new one to clicked position
			//Fill the input fields in screen form and modal
            map.addListener("click", (event) => {
            if (marker) {
                    marker.setMap(null); // Clear Current Marker
                }
                marker = new google.maps.Marker({
                    position: event.latLng,
                    map: map
                });
                let latitude = event.latLng.lat();
                let longitude = event.latLng.lng();

				var modalLatEl = $('#modal-lat');
				var modalLongEl = $('#modal-long');
				var formLatEl = $('#form-lat');
				var formLongEl = $('#form-long');
				
				modalLatEl.val(latitude);
				modalLongEl.val(longitude);
				formLatEl.val(latitude);
				formLongEl.val(longitude);
            })
        }
        window.initMap = initMap;
    </script>
	
    <style>
        #map {
          height: 500px;
          width: 750px;
          margin-left: 20px;
          margin-top: 20px;
        }
        #open-map-button svg{
          width: 20px;
          height: 20px;
          margin-top: 1px;
        }

		[data-bs-dismiss="modal"].btn-link{
			border: 1px solid green;
			text-decoration: none;
			color: green;
		}
		[data-bs-dismiss="modal"].btn-link:hover{
			color: #fff;
			background-color: green;
			
		}
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxWzU_rlK4h3Q6v2P2UTW7OKLqt9GtELE&callback=initMap&v=weekly"
      defer
    ></script>

  </body>
</html>
  