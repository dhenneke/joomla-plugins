# sp-youtube-gdpr-plugin

A GDPR-compliant YouTube video addon for [SP Page Builder](https://www.joomlashine.com/joomla-extensions/sp-page-builder.html). Instead of embedding an iframe that sets cookies the moment a page loads, this plugin displays a static thumbnail card. Visitors are only sent to YouTube when they actively click the link — no tracking cookies, no third-party requests until consent is given.

> **Disclaimer:** This plugin is not affiliated with or endorsed by YouTube or Google LLC. YouTube is a trademark of Google LLC.

---

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later

---

## Installation

1. Run `build.bat` to produce `out/plg_sppagebuilder_ytgdpr.zip`, or download a release zip.
2. In the Joomla backend go to **System → Install → Extensions** and upload the zip.
3. Go to **System → Plugins**, find **SP Page Builder – YouTube GDPR** and enable it.

---

## Configuration

In the plugin settings (System → Plugins → SP Page Builder – YouTube GDPR):

| Parameter | Default | Description |
|---|---|---|
| Cache Duration (seconds) | 3600 | How long thumbnails and video metadata are cached on the server before being refreshed. |

---

## Usage

After installation, a new **YouTube Embedder** addon appears in the SP Page Builder addon panel under the *MyAddons* category.

1. Drag the addon onto your page.
2. Paste any supported YouTube URL into the **YouTube Video URL** field:
	- `https://www.youtube.com/watch?v=...`
	- `https://youtu.be/...`
	- `https://www.youtube.com/shorts/...`
	- `https://www.youtube.com/embed/...`
3. Save the page.

The addon renders a thumbnail card with the video title and channel name. Clicking it opens the video on YouTube.com — no iframe, no cookies.

---

## How it works

- The video ID is extracted and validated from the URL server-side.
- The thumbnail is fetched from YouTube's image CDN and cached on the server.
- Video title and channel name are fetched via the public oEmbed endpoint and cached on the server.
- The frontend renders pure HTML — no JavaScript, no iframes, no third-party requests.

---

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html. See [LICENSE](LICENSE).
