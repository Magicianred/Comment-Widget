/**
 * Plugin functions file.
 *
 */
if( "undefined"==typeof jQuery )throw new Error( "Advanced Comments Widget's JavaScript requires jQuery" );

(function ($) {

    'use strict';

	function change_avatar_div( e ){
		var field = $(e.currentTarget);
		var acw_avatar_div = field.closest('.acw-thumb-size-wrap').find('.acw-avatar');

		if( acw_avatar_div.length ) {
			var icon = $( '.acw-icon', acw_avatar_div);
			var size = parseInt ( ($.trim( field.val() ) * 1) + 0 );
			acw_avatar_div.css({
				'height' : size + 'px',
				'width' : size + 'px'
			});
			icon.css({ 'font-size' : size + 'px' });
		}

		return;
	};

	// Customizer Screen
	$('#customize-controls, #wpcontent').on( 'change', '.acw-thumb-size', function ( e ) {
		change_avatar_div( e );
		return;
	});

	// Customizer Screen
	$('#customize-controls, #wpcontent').on( 'keyup', '.acw-thumb-size', function ( e ) {
		setTimeout( function(){
			change_avatar_div( e );
		}, 300 );
		return;
	});



}(jQuery));