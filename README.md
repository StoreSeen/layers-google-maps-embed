# Layers Google Maps Embed

A small, reusable WordPress plugin that adds a Layers-compatible Google Maps Embed API widget.

It reuses the API key already stored by Layers at **Customizer > Site Settings > Additional Scripts > Google Maps API Key**. It does not load the Maps JavaScript API or call the Geocoding API.

## Features

- Google Maps Embed API `place` mode
- Address, place name, place ID, or optional coordinate input
- Zoom, height, roadmap/satellite, and accessible-title controls
- Lazy-loaded responsive iframe
- Layers Customizer selective refresh
- PHP 5.6+ compatibility for older Layers installations
- No JavaScript dependency

## Installation

1. Download the release ZIP.
2. In WordPress, open **Plugins > Add New > Upload Plugin**.
3. Upload and activate the ZIP.
4. Ensure the site's existing Google browser key has **Maps Embed API** enabled.
5. Add **Google Maps Embed** to a widget area in the Customizer.

## API key rollout

During migration, the existing per-site key can allow Maps Embed API alongside the APIs required by the old widget. Once the old widget has been removed and the public site has been verified, restrict that key to:

- The site's own HTTP referrers
- Maps Embed API only

Never commit an API key to this repository. The browser key is read at runtime from the Layers theme setting.

## Development

Run the dependency-free PHP smoke tests:

```sh
php tests/run.php
```

Create the installable ZIP on Windows:

```powershell
./scripts/build.ps1
```

## License

[MIT](LICENSE)

