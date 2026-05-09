# Contact Form Addon for SP Page Builder

A plain contact form addon for SP Page Builder with Joomla captcha plugin support via the official Joomla Captcha API.

This extension is a third-party addon and is not affiliated with or endorsed by JoomShaper.

## Features

- Fields: name, email, subject, message
- Optional required privacy policy consent checkbox with editor-based text so links can be embedded directly per addon instance
- Supports Joomla default captcha plugin (for example reCAPTCHA, hCaptcha, and compatible captcha plugins)
- Server-side validation and mail dispatch through Joomla Mailer
- Stateless signed form state token (HMAC + expiry) to reduce form tampering
- Same-origin request check (Origin/Referer) as defense-in-depth
- IP-based rate limiting through Joomla's cache

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later
- A working Joomla mail configuration

## Installation

1. Build plugin zips from repository root with `./build.sh`.
2. In Joomla backend go to **System -> Install -> Extensions** and upload the generated `out/plg_sppagebuilder_contactform-<version>.zip` file.
3. Enable **SP Page Builder - Contact Form** in **System -> Plugins**.

## Versioning

- The released plugin version is defined in `contactform.xml`.
- Keep `contactform.xml` and `CHANGELOG.md` in sync whenever you cut a release.

## Addon Configuration

- Recipient Email: target mailbox for this form instance
- Submit Button Label: custom text for the submit button per form instance
- Require Privacy Policy Consent: toggle a required consent checkbox per form instance
- Privacy Consent Text: editor field for the checkbox copy, including optional embedded links
- Enable Captcha: toggle captcha per form instance
- Captcha Type: choose default (global Joomla captcha) or a specific installed captcha plugin

## Usage

1. Open SP Page Builder and add the **Contact Form** addon.
2. Set recipient email per addon instance, then optionally set button label and captcha behavior.
3. Save and publish page.

## Notes

- Captcha rendering uses Joomla's configured default captcha plugin from Global Configuration.
- If no captcha plugin is configured and captcha is required, submission fails.
- Form feedback is rendered inline after submission.
- Submissions are limited to 5 same-origin attempts per IP address and addon instance per 15 minutes.
- Field placeholders mirror the configured field labels.
- The markup uses SP Page Builder form/grid classes such as `sppb-row`, `sppb-form-group`, `sppb-form-control`, and `sppb-form-check` where appropriate.
- The addon relies on SP Page Builder's built-in group spacing and form-control styling instead of adding its own outer layout or field padding.
- The textarea keeps a usable minimum height without forcing border, radius, or background styling onto the active template.

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html. See ../LICENSE.
