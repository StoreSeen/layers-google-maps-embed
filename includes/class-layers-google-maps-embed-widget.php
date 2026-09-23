<?php
/**
 * Layers Google Maps Embed widget.
 *
 * @package LayersGoogleMapsEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Layers_Google_Maps_Embed_Widget' ) ) {
	class Layers_Google_Maps_Embed_Widget extends Layers_Widget {

		/**
		 * Default widget values.
		 *
		 * @var array
		 */
		public $defaults = array();

		/**
		 * Set up the Layers-compatible widget.
		 */
		public function __construct() {
			$this->widget_title = __( 'Google Maps Embed', 'layers-google-maps-embed' );
			$this->widget_id = 'google-maps-embed';

			$this->defaults = array(
				'location'    => '',
				'coordinates' => '',
				'zoom'        => 14,
				'height'      => 200,
				'maptype'     => 'roadmap',
				'map_title'   => __( 'Google map', 'layers-google-maps-embed' ),
			);

			$widget_ops = array(
				'classname'                   => 'layers-google-maps-embed-widget',
				'description'                 => __( 'Displays a Google map using the no-charge Maps Embed API and the API key already stored by Layers.', 'layers-google-maps-embed' ),
				'customize_selective_refresh' => true,
			);

			$control_ops = array(
				'width'   => 660,
				'height'  => null,
				'id_base' => 'layers-google-maps-embed',
			);

			parent::__construct(
				'layers-google-maps-embed',
				$this->widget_title,
				$widget_ops,
				$control_ops
			);
		}

		/**
		 * Render the widget.
		 *
		 * @param array $args     Sidebar display arguments.
		 * @param array $instance Saved widget values.
		 */
		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$location = trim( (string) $instance['location'] );
			$coordinates = trim( (string) $instance['coordinates'] );
			$query = '' !== $coordinates ? $coordinates : $location;
			$api_key = layers_google_maps_embed_get_api_key();

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( '' === $api_key || '' === $query ) {
				if ( current_user_can( 'edit_theme_options' ) ) {
					$message = '' === $api_key
						? __( 'Add a Google Maps API Key under Site Settings > Additional Scripts to display this map.', 'layers-google-maps-embed' )
						: __( 'Enter a map location or coordinates to display this map.', 'layers-google-maps-embed' );

					echo '<p class="layers-google-maps-embed-notice">' . esc_html( $message ) . '</p>';
				}

				echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}

			$height = max( 200, min( 1600, absint( $instance['height'] ) ) );
			$zoom = max( 0, min( 21, absint( $instance['zoom'] ) ) );
			$maptype = in_array( $instance['maptype'], array( 'roadmap', 'satellite' ), true ) ? $instance['maptype'] : 'roadmap';
			$map_title = trim( (string) $instance['map_title'] );

			if ( '' === $map_title ) {
				$map_title = __( 'Google map', 'layers-google-maps-embed' );
			}

			$embed_url = layers_google_maps_embed_build_url( $api_key, $query, $zoom, $maptype );
			?>
			<div class="layers-google-maps-embed" style="height: <?php echo esc_attr( $height ); ?>px; overflow: hidden; width: 100%;">
				<iframe
					title="<?php echo esc_attr( $map_title ); ?>"
					src="<?php echo esc_url( $embed_url ); ?>"
					width="100%"
					height="<?php echo esc_attr( $height ); ?>"
					style="border: 0; display: block; height: <?php echo esc_attr( $height ); ?>px; width: 100%;"
					loading="lazy"
					allowfullscreen
					referrerpolicy="strict-origin-when-cross-origin"
				></iframe>
			</div>
			<?php

			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Sanitize values before saving.
		 *
		 * @param array $new_instance Submitted values.
		 * @param array $old_instance Existing values.
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['location'] = isset( $new_instance['location'] ) ? sanitize_text_field( $new_instance['location'] ) : '';
			$instance['coordinates'] = isset( $new_instance['coordinates'] ) ? sanitize_text_field( $new_instance['coordinates'] ) : '';
			$instance['zoom'] = isset( $new_instance['zoom'] ) ? max( 0, min( 21, absint( $new_instance['zoom'] ) ) ) : 14;
			$instance['height'] = isset( $new_instance['height'] ) ? max( 200, min( 1600, absint( $new_instance['height'] ) ) ) : 200;
			$instance['maptype'] = isset( $new_instance['maptype'] ) && in_array( $new_instance['maptype'], array( 'roadmap', 'satellite' ), true ) ? $new_instance['maptype'] : 'roadmap';
			$instance['map_title'] = isset( $new_instance['map_title'] ) ? sanitize_text_field( $new_instance['map_title'] ) : '';

			return $instance;
		}

		/**
		 * Render the Customizer/widget form.
		 *
		 * @param array $instance Saved widget values.
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );
			$key_is_set = '' !== layers_google_maps_embed_get_api_key();
			?>
			<div class="layers-container-large">
				<section class="layers-accordion-section layers-content">
					<p class="layers-form-item">
						<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php echo esc_html__( 'Google Maps Location', 'layers-google-maps-embed' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['location'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g. 122 Ballymacash Road, Lisburn, BT28 3EZ', 'layers-google-maps-embed' ); ?>">
					</p>

					<p class="layers-form-item">
						<label for="<?php echo esc_attr( $this->get_field_id( 'coordinates' ) ); ?>"><?php echo esc_html__( 'Google Maps Latitude & Longitude (Optional)', 'layers-google-maps-embed' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'coordinates' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'coordinates' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['coordinates'] ); ?>" placeholder="<?php echo esc_attr__( 'e.g. 54.5260, -6.0580', 'layers-google-maps-embed' ); ?>">
						<small><?php echo esc_html__( 'Coordinates take priority when both fields are filled.', 'layers-google-maps-embed' ); ?></small>
					</p>

					<div class="layers-row clearfix">
						<p class="layers-form-item layers-column layers-span-4">
							<label for="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>"><?php echo esc_html__( 'Zoom Level', 'layers-google-maps-embed' ); ?></label>
							<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'zoom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'zoom' ) ); ?>">
								<?php for ( $zoom = 0; $zoom <= 21; $zoom++ ) : ?>
									<option value="<?php echo esc_attr( $zoom ); ?>" <?php selected( absint( $instance['zoom'] ), $zoom ); ?>><?php echo esc_html( $zoom ); ?></option>
								<?php endfor; ?>
							</select>
						</p>

						<p class="layers-form-item layers-column layers-span-4">
							<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php echo esc_html__( 'Map Height (px)', 'layers-google-maps-embed' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" min="200" max="1600" step="1" value="<?php echo esc_attr( absint( $instance['height'] ) ); ?>">
						</p>

						<p class="layers-form-item layers-column layers-span-4">
							<label for="<?php echo esc_attr( $this->get_field_id( 'maptype' ) ); ?>"><?php echo esc_html__( 'Map Type', 'layers-google-maps-embed' ); ?></label>
							<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'maptype' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maptype' ) ); ?>">
								<option value="roadmap" <?php selected( $instance['maptype'], 'roadmap' ); ?>><?php echo esc_html__( 'Roadmap', 'layers-google-maps-embed' ); ?></option>
								<option value="satellite" <?php selected( $instance['maptype'], 'satellite' ); ?>><?php echo esc_html__( 'Satellite', 'layers-google-maps-embed' ); ?></option>
							</select>
						</p>
					</div>

					<p class="layers-form-item">
						<label for="<?php echo esc_attr( $this->get_field_id( 'map_title' ) ); ?>"><?php echo esc_html__( 'Accessible Map Title', 'layers-google-maps-embed' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'map_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'map_title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['map_title'] ); ?>">
					</p>

					<p class="description">
						<?php
						echo esc_html(
							$key_is_set
								? __( 'The existing Layers Google Maps API key is available. This widget uses only the Maps Embed API and does not load the Maps JavaScript or Geocoding APIs.', 'layers-google-maps-embed' )
								: __( 'No Layers Google Maps API key was found. Add it under Site Settings > Additional Scripts.', 'layers-google-maps-embed' )
						);
						?>
					</p>
				</section>
			</div>
			<?php
		}
	}
}

