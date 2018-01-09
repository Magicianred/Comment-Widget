<?php
/**
 * Comments Widget
 *
 * @package ACW_Recent_Comments
 *
 * @license     http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @version     1.1.2
 *
 * Plugin Name: Comments Widget
 * Description: A highly customizable recent comments widget.
 * Version:     1.1.2
 * Domain Path: /lang
 * License:     GPL-2.0+
 */


// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * Instantiates the main Comment Widget instance
 *
 * @since 1.0
 */
function _acw_init() {

	include dirname( __FILE__ ) . '/inc/class-acw-recent-comments-utilities.php';
	include dirname( __FILE__ ) . '/inc/class-widget-acw-recent-comments.php';
	include dirname( __FILE__ ) . '/inc/class-acw-recent-comments.php';

	$ACW_Recent_Comments = new ACW_Recent_Comments( __FILE__ );
	$ACW_Recent_Comments->init();

}
add_action( 'plugins_loaded', '_acw_init', 99 );
