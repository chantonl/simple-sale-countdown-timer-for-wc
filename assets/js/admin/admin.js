jQuery( function ( $ ) {

	// Admin JS
	var ssct_admin = {

	    // Init admin JS
		init: function() {
			this.init_datepicker();

			$('body')
			.on( 'keyup', '.decimal', this.validate_input )
			.on( 'keyup', '.CodeMirror-lines', this.disable_save_if_error )
			.on( 'click', '#nav-tab-type a.nav-tab', this.toggle_nav_tab );

			if ( $( '#custom_css' ).length > 0 ) {
				wp.codeEditor.initialize( $( '#custom_css' ), ssct_admin_params.cm_settings );
			}
		},

	    // If codemirror has error diasble saving of setting to avoid saving of erroneous css.
		disable_save_if_error: function() {
			$( '#ssct_save_settings' ).attr( 'disabled', $( '.CodeMirror-lint-marker-error' ).length > 0 );
		},

	    // Validate input fields to allow only decimal number as the offered pruce.
		validate_input: function() {
			var val = $( this ).val();
    		
    		if ( isNaN( val ) ) {
         		val = val.replace(/[^0-9\.]/g,'');
         		
         		if ( val.split( '.' ).length > 2 )  val = val.replace(/\.+$/,"");
    		}
    		
    		$( this ).val(val);			
		},

	    // Handle toggle of navigation tab on manage page.
		toggle_nav_tab: function(e) {
			e.preventDefault();
			
			var sale_on = $(this).data('sale_on');
			$("#nav-tab-type #ssct-is-on").val( sale_on );
			
			$("#nav-tab-type").find('.nav-tab-active').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');

			ssct_admin.toggle_select2_search( sale_on );
		},

		// Date picker fields.
		date_picker_select: function( datepicker ) {
			var option         = $( datepicker ).hasClass( 'ssct-start-date' ) ? 'minDate' : 'maxDate',
				otherDateField = 'minDate' === option ? $( '.ssct-end-date' ) : $( '.ssct-start-date' ),
				date           = $( datepicker ).datepicker( 'getDate' );

			$( otherDateField ).datepicker( 'option', option, date );
			$( datepicker ).change();		
		},

		// Handle date picker fields.
		init_datepicker: function() {
			$( '.ssct-datepicker' ).datepicker({
				defaultDate: '',
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				onSelect: function() {
					ssct_admin.date_picker_select( $( this ) );
				}
			});
		},

		// Toggle search on manage page.
		toggle_select2_search: function( sale_on = 'products' ) {
			if ( 'categories' == sale_on ) {
				$( '#ssct-products-container' ).addClass( 'ssct-hidden');		
				$( '#ssct-products-container select' ).attr( 'required', false );		
				
				$( '#ssct-categories-container' ).removeClass('ssct-hidden');		
				$( '#ssct-categories-container select' ).attr( 'required', true );		
			}

			if ( 'products' == sale_on ) {
				$( '#ssct-products-container' ).removeClass('ssct-hidden');		
				$( '#ssct-categories-container select' ).attr( 'required', false );		
				
				$( '#ssct-categories-container' ).addClass( 'ssct-hidden');		
				$( '#ssct-products-container select' ).attr( 'required', true );		
			}
		},
	};	
		
	ssct_admin.init();
});		
