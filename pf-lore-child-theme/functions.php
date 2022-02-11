<?php
/**
 * Theme functions.
 *
 * @package pf-lore-child-theme
 */

 /**
 * We include modules of theme, order matters!
 */
require_once __DIR__ . '/customizer/class-pf-theme-customizer.php';


/**
 * Theme styles
 *
 * @return void
 */
function pf_enqueue_styles() {

	wp_enqueue_style( 'lore-child-style', get_stylesheet_directory_uri() . '/style.css', array(), '1.2' );

	if ( is_rtl() ) {
		wp_enqueue_style( 'lore-rtl', get_template_directory_uri() . '/rtl.css', array(), '1', 'screen' );
	}
}
add_action( 'wp_enqueue_scripts', 'pf_enqueue_styles', 100 );


/**
 * Editor styles
 *
 * @return void
 */
function pf_editor_styles() {
	wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri() . '/editor-style.css', array(), '1.2' );
}

add_action( 'admin_head', 'pf_editor_styles' );
