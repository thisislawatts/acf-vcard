(function(window, undefined){

	var $ = window.jQuery;
	var google = window.google;
	
	var VCard = {} || VCard;

	VCard.init = function() {

		var _this = this;

		_this.Maps = [];

		_this.$maps = $('.acf-vcard--map').each(function() {
			_this.Maps.push( new VCardMap( $(this).attr('id') ) );
		});

	};

	var VCardMap = function( unique_id ) {
		var _this = this;
		this.$map = $('#' + unique_id );
		this.$wrap = this.$map.parents('.acf-vcard--map-container').first();
		this.$lat = this.$wrap.find('input[id*="latitude"]');
		this.$lng = this.$wrap.find('input[id*="longitude"]');
		this.$button = $('<button class="button acf-vcard--find-address">Update from Address</button>');
		this.$vcard = this.$wrap.parents('.acf-vcard');

		this.$wrap.append( this.$button );

		this.setup();

		this.$wrap.on('click', '.acf-vcard--find-address', function(event) {
			event.preventDefault();
			_this.getAddress();
		} );
	};

	VCardMap.prototype.getAddress = function() {
		var _this = this;
		var address = [];
		var geocoder = new google.maps.Geocoder();


		_this.$vcard.find('.acf-vcard-fieldset input').each(function() {
			var val = $(this).val();
			if ( val ) address.push(val);
		});

		geocoder.geocode({
			address: address.join(',')
		}, function( results, status ) {
			console.log( results, status );

			if ( status === "OK") {
				_this.setPosition( results[0].geometry.location );
			}
		});
	};

	VCardMap.prototype.getPosition = function() {
		var lat = this.$lat.val() || 51.532365,
			lng = this.$lng.val() || -0.099363;
		return new google.maps.LatLng(lat, lng);
	};

	VCardMap.prototype.setPosition = function(latlng) {
		this.map.setCenter(latlng);
		this.marker.setPosition(latlng);
		this.updatePosition(latlng);
	};

	VCardMap.prototype.setup = function() {
		google.maps.visualRefresh = true;
		var _this = this;
		var mapOptions = {
				zoom: 10,
				center: _this.getPosition(),
				mapTypeId:google.maps.MapTypeId.ROADMAP,
				styles:[{
					featureType:"poi",
					elementType:"labels",
					stylers:[{
						visibility: 'off'
					}]
				}]
			};

		this.$map.css({
			'left'		: this.$map.position().left * -1,
			'height'	: '250px',
			'width'		: this.$map.width() + this.$map.position().left * 2
		});
		var map = new google.maps.Map( this.$map.get(0), mapOptions );

		this.map = map;
		this.addMarker( map.getCenter() );
	};

	VCardMap.prototype.addMarker = function( latlng ) {
		this.marker = new google.maps.Marker({
			map			: this.map,
			position	: latlng,
			draggable	: true
		});

		this.map.setCenter(latlng);
		this.dragDropMarker();
	};

	VCardMap.prototype.updatePosition = function(latlng) {
		this.$lng.val(latlng.lng());
		this.$lat.val(latlng.lat());
	};

	VCardMap.prototype.dragDropMarker = function() {
		var _this = this;
		google.maps.event.addListener(this.marker, 'dragend', function(mapEvent) {
			_this.updatePosition( mapEvent.latLng );
		});
	};

	$(document).live('acf/setup_fields', function(e, postbox){
		console.log("Setup", e, postbox );
		
		$(postbox).find('.field_type-vcard').each(function(){
			VCard.init();
			// $(this).add_awesome_stuff();
		});
	
	});

}(window));
