<?php
/**
 * Plugin Name: Layers Google Maps Embed
 * Plugin URI: https://github.com/StoreSeen/layers-google-maps-embed
 * Description: Adds a Layers-compatible Google Maps Embed API widget that reuses the API key stored under Site Settings > Additional Scripts.
 * Version: 0.1.1
 * Author: StoreSeen
 * Author URI: https://github.com/StoreSeen
 * License: MIT
 * License URI: https://opensource.org/license/mit/
 * Text Domain: layers-google-maps-embed
 * Requires at least: 4.9
 * Requires PHP: 5.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LAYERS_GOOGLE_MAPS_EMBED_VERSION', '0.1.1' );
define( 'LAYERS_GOOGLE_MAPS_EMBED_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Register the widget after Layers has registered its base widget class.
 *
 * Layers loads Layers_Widget on widgets_init at priority 20, so this callback
 * must run later. Plugins load before themes, making the default same-priority
 * ordering too early.
 */
function layers_google_maps_embed_register_widget() {
	if ( ! class_exists( 'Layers_Widget' ) ) {
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-layers-google-maps-embed-widget.php';
	register_widget( 'Layers_Google_Maps_Embed_Widget' );
}
add_action( 'widgets_init', 'layers_google_maps_embed_register_widget', 30 );

/**
 * Explain why the widget is unavailable when Layers is not the active theme.
 */
function layers_google_maps_embed_admin_notice() {
	if ( class_exists( 'Layers_Widget' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p><?php echo esc_html__( 'Layers Google Maps Embed is active, but its widget requires the Layers theme.', 'layers-google-maps-embed' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'layers_google_maps_embed_admin_notice' );
