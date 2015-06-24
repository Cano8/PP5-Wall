var colours = ["rgb(146,161,171)", "rgb(152,203,74)", "rgb(255,200,0)", "rgb(241,95,116)", "rgb(84,129,230)", "rgb(110,45,110)"];
var numberOfColours = colours.length
var lastColour = 0;
var delay = 8000;
var fadeDuration = 1500;
var APIkey = "AIzaSyCBb7hqlfqN9HIvcJIjixPZB7rLoOnSnAQ" //this is a really fucking dumb place to store it

var bcgChangerTween;
var modalPopInTween;

function colourChange() {
	//console.log("log test "+counter);
	var colour = Math.floor( ( Math.random() * numberOfColours ) ); 
	if (colour == lastColour)
		colour++;
	if (colour == numberOfColours )
		colour = 0;
	lastColour = colour;
	//$( 'body' ).animate( { backgroundColor: colours[colour] }, fadeDuration);
	//$( 'body' ).css( { "background-color": colours[colour] } )
	var obj = $( 'body' );
	bcgChangerTween = TweenLite.to( 'body', fadeDuration/1000, { backgroundColor: colours[colour], ease: Sine.easeIn } );
}

function requestForm( n ) {
	$.ajax({
		type: "POST",
		url:  '/PP5-Wall/web/index.php',
		data: {  requestForm: n },
		cache: false
	}).done ( function( data ) {
		var d = $(data);
		findLocation( $( d ).find( "#form_location" ) );
		$( d ).find( "#form_number" ).val( n );
		$( "#modal1" ).html( d );
	}).fail( function() {
		alert( 'Ajax request failed (form create).' );
	});
}

function modalPopIn ( dialog ) {
	bcgChangerTween.kill();
	TweenLite.from( dialog, 0.8, { scale: 0.4, ease: Elastic.easeOut } );
}

function findLocation( target ) {
		var lat = 0;
		var lon = 0;
		var subLoc = '';
		var loc = 'Could not find localisation.';
		
		if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition( function ( position ) {
				lat = position.coords.latitude;
				lon = position.coords.longitude;

				var request = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + lon + "&result_type=street_address&key=" + APIkey;
				$.get( request , function( json ) {
					if ( json.status == "OK" ) {
						$.each(json.results[0].address_components, function(i, v) {
							if ( v.types[0].search( new RegExp( /sublocality_level_1/i ) ) != -1 )
								subLoc = v.short_name;
							if ( v.types[0].search( new RegExp( /locality/i ) ) != -1 )
								loc = v.short_name;
						});
						if ( subLoc != '' ) {
							target.val( loc + " (" + subLoc + ")" );;
						} else
							target.val( loc );
					} else
						target.val( "No location found." );
				});
			});
		} else { 
			alert ( "Geolocation is not supported by this browser." );
		}
		target.val( "Finding location failed." );
	}

$(document).ready(function () {

	colourChange();
	setInterval( function () { colourChange() }, delay);
	
	$( '#myModal' ).keypress( function ( e ) {
		if ( e.which == 13 ) {
			e.preventDefault();
			$( '#editForm' ).submit();
		}
	});
	
	//form request
	$( '#entries' ).on( 'click', '.modifier', function () { 
		requestForm( $(this).parent().parent().attr( 'id' ).substr(5) );
		modalPopIn ( $( '.modal-dialog' ) );
	});
	
	$( '#entries' ).on( 'click', '.remover', function() {
		n = $(this).parent().parent().attr( 'id' ).substr(5);
		$.ajax({
			type: 'POST',
			url:  '/PP5-Wall/web/index.php',
			data: {  remove: n },
			cache: false
		}).done ( function( data ) {
			$( "#entries" ).html( data );
		}).fail( function() {
			alert( 'Ajax request failed (remove).' );
		});
	});
	
	$( "#addEntry" ).on( 'click', function() {
		$.ajax({
			type: "POST",
			url:  '/PP5-Wall/web/index.php',
			data: {  add: "" },
			cache: false
		}).done ( function( data ) {
			$( '#entries' ).html( data );
			requestForm( $( '.entry' ).length - 1 );
			$( '#myModal' ).modal('show');
			modalPopIn ( $( '.modal-dialog' ) );
		}).fail( function() {
			alert( 'Ajax request failed (add).' );
		});
	});

	$( '#editForm' ).submit(function(e) {
		e.preventDefault();

		//var thisForm = this;
		//var htmlData = $( '#editForm :input' );
		//var formData = new FormData($('#editForm')[0]);
		//alert ( JSON.stringify(formData) );
		var htmlData = new FormData(this);
		
		$.ajax({
			type: 'POST',
			url:  '/PP5-Wall/web/index.php',
			data: htmlData,
			cache: false,
			contentType: false,
			processData: false
		}).done ( function( data ) {
			if ( $( data ).find("li").length == 0 ) {
				//alert ( data );	//!
				$( '#myModal' ).modal( 'hide' );
				$( '#entries' ).html( data );
			} else {
				$( '#modal1' ).html( data );
			}
		}).fail( function(XMLHttpRequest, textStatus, errorThrown)  {
			alert( 'Failed to submit the form. ('+ JSON.stringify (XMLHttpRequest) + ")." );
		});
	});
	
	$( '#entries' ).on( 'mouseenter', '.entry', function () { 
		$(this).css( { "-webkit-box-shadow":  "0px 0px 10px 2px rgba(255, 255, 255, 1)"} );
		$(this).css( { "-moz-box-shadow":  "0px 0px 10px 2px rgba(255, 255, 255, 1)"} );
		$(this).css( { "box-shadow":  "0px 0px 10px 2px rgba(255, 255, 255, 1)"} );
	});
	
	$( '#entries' ).on( 'mouseleave', '.entry', function () { 
		$(this).css( { "-webkit-box-shadow":  "none"} );
		$(this).css( { "-moz-box-shadow":  "none"} );
		$(this).css( { "box-shadow":  "none"} );
	});
});