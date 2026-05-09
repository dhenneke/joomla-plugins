# Joomla Plugins

This repository contains custom Joomla plugins, including SP Page Builder addons.

## Plugins

- `plg_sppagebuilder_ytgdpr`
   - GDPR-friendly YouTube thumbnail/link addon
   - README: `plg_sppagebuilder_ytgdpr/README.md`

- `plg_sppagebuilder_contactform`
   - Plain contact form addon (name, email, subject, message)
   - Uses Joomla captcha plugins through official captcha API
   - Includes basic anti-abuse hardening (signed payload validation + IP rate limiting)
   - README: `plg_sppagebuilder_contactform/README.md`

## Build

Run from repository root:

```bash
./build.sh
```

This creates:

- `out/plg_sppagebuilder_ytgdpr-<version>.zip`
- `out/plg_sppagebuilder_contactform-<version>.zip`

## Development

Static analysis is handled with PHPStan from the repository root.

### Repository Setup For Checks

The lint setup expects this local layout:

```text
joomla-plugins/
├── .joomla/
├── .sppagebuilder/   # optional
├── vendor/
├── composer.json
└── phpstan-bootstrap.php
```

Required setup:

1. Install the repo dev dependencies:

```bash
composer install
```

2. Place a Joomla installation in `.joomla/`.

PHPStan boots real Joomla code through `phpstan-bootstrap.php`, so `.joomla/` must contain at least:

- `.joomla/libraries/bootstrap.php`
- `.joomla/libraries/vendor/autoload.php`

The simplest way is to extract a Joomla release from GitHub into `.joomla/`. The latest releases are published at:

- `https://github.com/joomla/joomla-cms/releases`

For local setup, download a release archive from that page and extract it into `.joomla/` at the repository root.

If your Joomla copy does not include vendor dependencies yet, install them inside that Joomla tree before running the checks.

3. Optionally place an extracted SP Page Builder installation in `.sppagebuilder/`.

If `.sppagebuilder/` is present, the bootstrap loads the real `SppagebuilderAddons` and `SpAddonsConfig` classes for better IDE resolution and more accurate analysis. If it is absent, CI still works through minimal fallback types.

Installed dev dependency:

- `phpstan/phpstan` for repo-wide static analysis

Joomla CMS itself is not installed through this monorepo Composer setup. The repo depends on a local Joomla tree instead of a Packagist `require-dev` package.

### Run Checks

Run all checks from the repository root:

```bash
composer lint
```

Available commands:

- `composer lint` runs PHPStan across both plugins
- `composer lint:contactform` runs PHPStan only for the contact form plugin
- `composer lint:youtube` runs PHPStan only for the YouTube GDPR plugin

If you want full local IDE resolution while working on addon base classes, keep both of these directories available:

- `.joomla`
- `.sppagebuilder`

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html. See LICENSE files in repository/plugin folders.
