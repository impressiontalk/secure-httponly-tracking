<?php

/**
 *
 * @link              https://www.impression.co.uk
 * @since             1.0.0
 * @package           Secure_Httponly_Tracking
 *
 * @wordpress-plugin
 * Plugin Name:       Secure HTTPonly Tracking
 * Plugin URI:        www.impression.co.uk/secure-httponly-tracking
 * Description:       This extends the lifetime of Google Analytics cookies beyond the ITP lifetime, by relaying then and setting them server side.
 * Version:           1.0.0
 * Author:            Impression Digital
 * Author URI:        https://www.impression.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       secure-httponly-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SECURE_HTTPONLY_TRACKING_VERSION', '1.0.0' );



function secure_httponly_tracking_enqueue_script() {
   wp_enqueue_script( 'secure_httponly_tracking', plugin_dir_url( __FILE__ ) . 'cookies.js' );
}
add_action('wp_enqueue_scripts', 'secure_httponly_tracking_enqueue_script');

add_action( 'rest_api_init', function () {
  register_rest_route( 'secure_httponly_tracking/v1', 'secure-cookies', array(
    'methods' => 'POST',
    'callback' => 'handle_callback',
  ) );
});

function handle_callback( WP_REST_Request $request ) {

	$cookies = parse_cookies($request->get_header('cookie'));

	$referer = parse_url( $request->get_header('referer'), PHP_URL_HOST);

	$one_year = time()+31536000;

	$response = new WP_REST_Response("", 201);

	foreach ($cookies as $cookie => $value) {
		if (substr($cookie, 0, 1) == "_") {
			setcookie($cookie, $value, $one_year, "/", ".".$referer, TRUE, TRUE);
		}
	}

	setcookie('secure_cookies_set', '1', time()+3600, "/", ".".$referer);

	return $response;
}


function parse_cookies($str){
	$cookies = array();
  foreach(explode('; ',$str) as $k => $v){
    preg_match('/^(.*?)=(.*?)$/i',trim($v),$matches);
    $cookies[trim($matches[1])] = urldecode($matches[2]);
  }
	return $cookies;
}
