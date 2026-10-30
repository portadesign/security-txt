#!/usr/bin/env sh
set -eu
FILE="$(dirname "$0")/../public/.well-known/security.txt"
NEW=$(date -u -v+11m +%Y-%m-01T00:00:00.000Z 2>/dev/null || date -u -d '+11 months' +%Y-%m-01T00:00:00.000Z)
sed -i.bak "s|^Expires: .*|Expires: ${NEW}|" "$FILE" && rm -f "$FILE.bak"
echo "Expires: ${NEW}"
