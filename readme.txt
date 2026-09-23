=== Layers Google Maps Embed ===
Contributors: storeseen
Tags: layers, google maps, embed, map, widget
Requires at least: 4.9
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 0.1.1
License: MIT
License URI: https://opensource.org/license/mit/

Adds a Layers-compatible map widget powered by the Google Maps Embed API.

== Description ==

Layers Google Maps Embed adds a reusable widget to the WordPress Customizer. It reads the existing Google Maps API Key saved by Layers under Site Settings > Additional Scripts and renders a responsive Google Maps Embed API iframe.

The plugin does not load the Maps JavaScript API and does not make Geocoding API requests.

Widget controls include:

* Map location, address, place name, or place ID
* Optional latitude and longitude
* Zoom level
* Map height
* Roadmap or satellite view
* Accessible iframe title

== Installation ==

1. Upload and activate the plugin.
2. Confirm Maps Embed API is enabled for the site's existing browser key.
3. In the Customizer, open Site Settings > Additional Scripts and confirm the Google Maps API Key is populated.
4. Add the Google Maps Embed widget to the required Layers widget area.
5. Configure the location, zoom, and height.

For production, restrict each browser key by website referrer and restrict its API access to Maps Embed API after the previous JavaScript/Geocoding map has been removed.

== Changelog ==

= 0.1.1 =
* Register the widget after Layers loads its base widget class.

= 0.1.0 =
* Initial test release.
