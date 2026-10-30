#!/usr/bin/env sh
set -eu
[ $# -ge 1 ] || { echo "usage: apply.sh <project-dir> [public-host]"; exit 1; }
TARGET=$(cd "$1" && pwd)
HOST=${2:-example.com}
SRC="$(cd "$(dirname "$0")" && pwd)/files"
EXPIRES=$(date -u -v+11m +%Y-%m-01T00:00:00.000Z 2>/dev/null || date -u -d '+11 months' +%Y-%m-01T00:00:00.000Z)

cd "$SRC"
find . -type f | sed 's|^\./||' | while read -r f; do
    if [ -e "$TARGET/$f" ] && [ "${FORCE:-0}" != 1 ]; then
        echo "skip   $f (exists; FORCE=1 to overwrite)"
        continue
    fi
    mkdir -p "$TARGET/$(dirname "$f")"
    sed -e "s|__EXPIRES__|$EXPIRES|" -e "s|__HOST__|$HOST|" "$f" > "$TARGET/$f"
    echo "write  $f"
done
chmod +x "$TARGET/bin/security-txt-check.php" "$TARGET/bin/security-txt-renew.sh" "$TARGET/.githooks/pre-push"
git -C "$TARGET" config core.hooksPath .githooks

cat <<MSG

Next in $TARGET:
  1. include .gitlab/security-check.yml in .gitlab-ci.yml (GitHub: workflow already in place)
  2. php bin/security-txt-check.php
MSG
