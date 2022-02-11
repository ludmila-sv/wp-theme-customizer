/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {
	const settingNames = window.PF_THEME_CUSTOM_SETTINGS || {};

	const root = document.documentElement;

	Object.keys( settingNames ).forEach( initPFCustomizerSetting );

	function initPFCustomizerSetting( settingName ) {
		const cssPropertyName = settingNames[ settingName ];
		wp.customize( settingName, function( value ) {
			value.bind( function( newval ) {
				root.style.setProperty( cssPropertyName, newval );
			} );
		} );
	}
} )( jQuery );
