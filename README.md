# SP Page Builder Plugins Monorepo

This repository contains multiple custom Joomla plugins for SP Page Builder.

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

```bat
build.bat
```

This creates:

- `out/plg_sppagebuilder_ytgdpr.zip`
- `out/plg_sppagebuilder_contactform.zip`

## Requirements

- Joomla 4.x or 5.x
- SP Page Builder 4.x or later

## License

GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html. See LICENSE files in repository/plugin folders.
