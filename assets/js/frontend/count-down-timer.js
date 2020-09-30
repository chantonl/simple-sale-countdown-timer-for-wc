jQuery( function ( $ ) {

	if ( typeof ssct_count_down_timer_params === 'undefined' ) return;

	var timer_data = [];
	
	// Get timer html
	function ssct_get_timer_html( hours, minutes, seconds ) {
		var timer_html = '';

		switch( ssct_count_down_timer_params.settings.countdown_timer.format ) {
			case 'simple':
				timer_html = "&nbsp;" + hours + "h" + '&nbsp;' + minutes + "m" + '&nbsp;' + seconds + "s";
				break;
			
			case 'colon':
				timer_html = "&nbsp;" + hours + ":" + minutes + ":" + seconds;
				break;
		} 

		return timer_html;
	}

	// Properly pad the timer digits  
	function pad( number, length ) {
   
    	var str = '' + number;
    	
    	while (str.length < length) {
        	str = '0' + str;
    	}
   
    	return str;
	}

	// Start countdown timer  
	function ssct_start_countdown( element_id ) {
	    var seconds = timer_data[ element_id ].remaining;
		
	    var hours             = pad( Math.floor( seconds / 60 / 60 ), 2	);
	    var minutes           = pad( Math.floor( ( seconds / 60 ) ) - ( hours * 60 ), 2 );
	    var remaining_seconds = pad( seconds % 60, 2 );


	    if ( seconds === 0 ) {
	        clearInterval( timer_data[element_id].timer_id );
	        $( '#' + element_id ).html( ssct_count_down_timer_params.settings.texts.finish );
	        $( 'body' ).trigger( 'ssct_sale_ended' )
	    } else {
	   		$( '#' + element_id + ' .ssct-timer' ).html( ssct_get_timer_html( hours, minutes, remaining_seconds ) );
	        seconds--;
	    }
	   
	    timer_data[ element_id ].remaining = seconds;
	}

	// Init countdown timer  
	function ssct_init_timer( element_id, seconds  ) {
	   	
	   	if ( element_id.length > 0 && seconds > 0 ) {
		   	$( '#' + element_id + ' .ssct-text' ).text( ssct_count_down_timer_params.settings.texts.prefix );
			
		   	if ( 'undefined' != typeof timer_data[ element_id ] ) {
		   		clearInterval( timer_data[ element_id ].timer_id );
		   	} 
		    
		    timer_data[ element_id ] = {
		        remaining : seconds,
		        timer_id  : setInterval( function () {
		            ssct_start_countdown( element_id );
		        }, 1000 )
		    };
		    
		    timer_data[ element_id ].timer_id;
	   	}
	}

	// Show countdown timer for product(s)
	$( '.ssct' ).each( function( i, obj )  {
		var element_id = $( obj ).attr( 'id' );
		var seconds    = $( obj ).data( 'seconds' );
		
		ssct_init_timer( element_id, seconds );	 	 
	});

	// Show countdown timer for a variation
	$( 'body' ).on( 'found_variation', function( event, variation ) {
		if ( 'ssct_seconds' in variation  && 'ssct_target' in variation ) {
			$( '.ssct' ).show();
			ssct_init_timer( variation.ssct_target, variation.ssct_seconds );
		} else {
			$( '.ssct' ).hide();
		}
	});

	// Hide all countdown timers on variation change to avoid display of mulitple countdown timers. 
	$( 'body' ).on( 'woocommerce_variation_select_change', function( event ) {
		$( '.ssct' ).hide();
	});
});