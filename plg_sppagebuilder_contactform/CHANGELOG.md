# Changelog

All notable changes to this plugin should be documented in this file.

The plugin version in `contactform.xml` should always match the latest released version documented here.

## 1.0.1

- Improve PHPStan 2.x compatibility by making layout input types explicit.
- Tighten contact form state token validation to return a stable typed payload shape.

## 1.0.0

- Initial release of the contact form addon.
- Stateless submission protection with signed form state and same-origin checks.
- Joomla captcha integration, server-side validation, and inline feedback rendering.