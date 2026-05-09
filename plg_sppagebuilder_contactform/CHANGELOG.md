# Changelog

All notable changes to this plugin should be documented in this file.

The plugin version in `contactform.xml` should always match the latest released version documented here.

## Unreleased

- Remove the optional form title field and render the addon without a heading.
- Add placeholders for the name, email, subject, and message fields based on their labels.
- Reorder addon settings so captcha options are grouped together again.
- Use SP Page Builder built-in form/grid classes in the form markup.
- Use `sppb-form-control` and `sppb-form-check` markup so fields and consent controls follow SP Page Builder's built-in styling.
- Rely on SP Page Builder's built-in group spacing instead of plugin-imposed outer layout or field padding.
- Keep a usable textarea height without forcing border, radius, or background styling.

## 1.1.0

- Add an optional privacy policy consent checkbox with editor-based custom text per addon instance.
- Require privacy consent server-side before processing form submissions.

## 1.0.1

- Improve PHPStan 2.x compatibility by making layout input types explicit.
- Tighten contact form state token validation to return a stable typed payload shape.

## 1.0.0

- Initial release of the contact form addon.
- Stateless submission protection with signed form state and same-origin checks.
- Joomla captcha integration, server-side validation, and inline feedback rendering.