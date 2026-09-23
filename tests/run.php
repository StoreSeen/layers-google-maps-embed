<?php
/**
 * Dependency-free smoke tests for shared plugin functions.
 */

define( 'ABSPATH', __DIR__ );

$test_theme_mods = array();

function get_theme_mod( $name, $default = false ) {
	global $test_theme_mods;
	return isset( $test_theme_mods[ $name ] ) ? $test_theme_mods[ $name ] : $default;
}

function layers_get_theme_mod( $name ) {
	global $test_theme_mods;
	return isset( $test_theme_mods[ 'helper:' . $name ] ) ? $test_theme_mods[ 'helper:' . $name ] : '';
}

function apply_filters( $name, $value ) {
	return $value;
}

function absint( $value ) {
	return abs( (int) $value );
}

require_once dirname( __DIR__ ) . '/includes/functions.php';

function assert_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		fwrite( STDERR, "FAIL: {$message}\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}
}

$test_theme_mods['layers-google-maps-api'] = ' existing-key ';
assert_same( 'existing-key', layers_google_maps_embed_get_api_key(), 'Reads and trims the existing Layers theme setting.' );

$test_theme_mods = array( 'helper:google-maps-api' => 'fallback-key' );
assert_same( 'fallback-key', layers_google_maps_embed_get_api_key(), 'Falls back to the Layers compatibility helper.' );

$url = layers_google_maps_embed_build_url( 'test-key', '122 Ballymacash Road, Lisburn', 14, 'roadmap' );
parse_str( parse_url( $url, PHP_URL_QUERY ), $query );
assert_same( 'https', parse_url( $url, PHP_URL_SCHEME ), 'Uses HTTPS.' );
assert_same( 'www.google.com', parse_url( $url, PHP_URL_HOST ), 'Uses the Google Maps Embed host.' );
assert_same( '/maps/embed/v1/place', parse_url( $url, PHP_URL_PATH ), 'Uses Embed API place mode.' );
assert_same( 'test-key', $query['key'], 'Includes the API key.' );
assert_same( '122 Ballymacash Road, Lisburn', $query['q'], 'Preserves and encodes the map query.' );
assert_same( '14', $query['zoom'], 'Includes the zoom level.' );
assert_same( 'roadmap', $query['maptype'], 'Includes the map type.' );

$bounded_url = layers_google_maps_embed_build_url( 'test-key', '54.5260, -6.0580', 99, 'invalid' );
parse_str( parse_url( $bounded_url, PHP_URL_QUERY ), $bounded_query );
assert_same( '21', $bounded_query['zoom'], 'Bounds the zoom level.' );
assert_same( 'roadmap', $bounded_query['maptype'], 'Rejects unsupported map types.' );

echo "All tests passed.\n";

