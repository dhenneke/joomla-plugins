# Changelog

All notable changes to this plugin should be documented in this file.

The plugin version in `youtube_gdpr.xml` should always match the latest released version documented here.

## 1.0.2

- Ignore oversized thumbnail responses before validating and caching downloaded images.

## 1.0.1

- Improve PHPStan 2.x compatibility by making layout input types explicit.
- Tighten cached and fetched oEmbed metadata normalization to accept only valid strings.

## 1.0.0

- Initial release of the YouTube GDPR addon.
- Thumbnail-based YouTube linking without iframe embedding on page load.
- Cached thumbnail and oEmbed metadata fetching.
