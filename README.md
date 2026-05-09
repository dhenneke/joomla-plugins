# Joomla Plugins

Custom Joomla plugins for SP Page Builder.

## Plugins

| Plugin | Description |
|---|---|
| `plg_sppagebuilder_contactform` | Contact form addon with Joomla captcha support, signed submissions, same-origin checks, and IP rate limiting. |
| `plg_sppagebuilder_ytgdpr` | GDPR-friendly YouTube addon that renders a cached thumbnail/link instead of loading an iframe on page load. |

Each plugin has its own README and changelog:

- `plg_sppagebuilder_contactform/README.md`
- `plg_sppagebuilder_contactform/CHANGELOG.md`
- `plg_sppagebuilder_ytgdpr/README.md`
- `plg_sppagebuilder_ytgdpr/CHANGELOG.md`

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later
- PHP and Composer for local checks

## Build

Run from the repository root:

```bash
./build.sh
```

Generated packages are written to `out/`:

- `plg_sppagebuilder_contactform-<version>.zip`
- `plg_sppagebuilder_ytgdpr-<version>.zip`

## Checks

Install dev dependencies first:

```bash
composer install
```

Run PHPStan:

```bash
composer lint
```

Target one plugin when needed:

```bash
composer lint:contactform
composer lint:youtube
```

Static analysis expects a Joomla installation in `.joomla/`. An extracted SP Page Builder copy in `.sppagebuilder/` is optional and improves local type resolution.

## License

GPL-3.0-or-later. See `LICENSE` and the plugin folders.
