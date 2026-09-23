<?php
/**
 * Shared plugin functions.
 *
 * @package LayersGoogleMapsEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read the API key already stored by Layers.
 *
 * Layers registers the Customizer setting as `layers-google-maps-api`. Older
 * releases also expose a prefixed helper, so the helper is retained as a
 * compatibility fallback.
 *
 * @return string
 */
function layers_google_maps_embed_get_api_key() {
	$key = get_theme_mod( 'layers-google-maps-api', '' );

	if ( empty( $key ) && function_exists( 'layers_get_theme_mod' ) ) {
		$key = layers_get_theme_mod( 'google-maps-api' );
	}

	$key = apply_filters( 'layers_google_maps_embed_api_key', $key );

	return is_string( $key ) ? trim( $key ) : '';
}

/**
 * Build a Google Maps Embed API place URL.
 *
 * @param string $api_key API key.
 * @param string $query   Address, place name, place ID, or coordinates.
 * @param int    $zoom    Zoom level from 0 to 21.
 * @param string $maptype Either roadmap or satellite.
 * @return string
 */
function layers_google_maps_embed_build_url( $api_key, $query, $zoom, $maptype ) {
	$zoom = max( 0, min( 21, absint( $zoom ) ) );
	$maptype = in_array( $maptype, array( 'roadmap', 'satellite' ), true ) ? $maptype : 'roadmap';

	$query_args = array(
		'key'     => trim( (string) $api_key ),
		'q'       => trim( (string) $query ),
		'zoom'    => $zoom,
		'maptype' => $maptype,
	);

	return 'https://www.google.com/maps/embed/v1/place?' . http_build_query( $query_args, '', '&', PHP_QUERY_RFC3986 );
}

