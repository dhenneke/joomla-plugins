# Contact Form Addon for SP Page Builder

A plain contact form addon for SP Page Builder with Joomla captcha plugin support via the official Joomla Captcha API.

This extension is a third-party addon and is not affiliated with or endorsed by JoomShaper.

## Features

- Fields: name, email, subject, message
- Supports Joomla default captcha plugin (for example reCAPTCHA, hCaptcha, and compatible captcha plugins)
- Server-side validation and mail dispatch through Joomla Mailer
- Stateless signed form state token (HMAC + expiry) to reduce form tampering
- Same-origin request check (Origin/Referer) as defense-in-depth
- No session or file storage required for form handling

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later
- A working Joomla mail configuration

## Installation

1. Build plugin zips from repository root with `build.bat`.
2. In Joomla backend go to **System -> Install -> Extensions** and upload `out/plg_sppagebuilder_contactform.zip`.
3. Enable **SP Page Builder - Contact Form** in **System -> Plugins**.

## Plugin Configuration

- Recipient Email: optional fallback mailbox when an addon instance has no recipient set

## Addon Configuration

- Recipient Email: target mailbox for this form instance
- Enable Captcha: toggle captcha per form instance
- Captcha Type: choose default (global Joomla captcha) or a specific installed captcha plugin

## Usage

1. Open SP Page Builder and add the **Contact Form** addon.
2. Set recipient email per addon instance, then optionally set title/button label and captcha behavior.
3. Save and publish page.

## Notes

- Captcha rendering uses Joomla's configured default captcha plugin from Global Configuration.
- If no captcha plugin is configured and captcha is required, submission fails.
- Form feedback is returned via redirect query parameters and rendered inline.

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html. See ../LICENSE.
