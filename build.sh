#!/usr/bin/env bash

set -euo pipefail

repo_root=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)

cd "$repo_root"

if ! command -v zip >/dev/null 2>&1; then
	echo "Error: zip command not found. Install the zip package before running build.sh." >&2
	exit 1
fi

rm -rf out
mkdir -p out

yt_version=$(sed -n 's:.*<version>\(.*\)</version>.*:\1:p' plg_sppagebuilder_ytgdpr/youtube_gdpr.xml | head -n 1)
contact_version=$(sed -n 's:.*<version>\(.*\)</version>.*:\1:p' plg_sppagebuilder_contactform/contactform.xml | head -n 1)

zip -rq "out/plg_sppagebuilder_ytgdpr-${yt_version}.zip" plg_sppagebuilder_ytgdpr
zip -rq "out/plg_sppagebuilder_contactform-${contact_version}.zip" plg_sppagebuilder_contactform