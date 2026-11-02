# security-txt

Create, check and renew a [RFC 9116](https://www.rfc-editor.org/rfc/rfc9116) `security.txt` from Composer. Public GitHub repository, not on Packagist. PHP ≥ 8.1, no dependencies.

## Why

`/.well-known/security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)) tells security researchers where to report a vulnerability in the site. The RFC requires an `Expires` date; after it passes, researchers treat the contact as stale and scanners flag the site. This package creates the file and fails the push or pipeline 30 days before expiry, so someone renews it in time.

## When the check fails

```
security.txt expires in 7 day(s), limit is 30.
```

Run `vendor/bin/security-txt renew`, commit `public/.well-known/security.txt`, push again. Nothing else changes; `renew` only moves `Expires` 11 months ahead.

## Install

```bash
composer config repositories.security-txt vcs https://github.com/portadesign/security-txt
composer require --dev portadesign/security-txt
vendor/bin/security-txt init pim.example.com
vendor/bin/security-txt hook
```

`init` writes `public/.well-known/security.txt` with `Contact: mailto:security@portadesign.cz`, `Expires` on the first day of the month 11 months ahead, `Preferred-Languages: cs, en` and `Canonical: https://<host>/.well-known/security.txt`. `hook` installs `.githooks/pre-push` running the check and sets `core.hooksPath`. Commit both.

## Commands

| Command | Does |
| --- | --- |
| `init <host> [--contact=] [--languages=] [--force]` | Create the file; refuses to overwrite without `--force` |
| `check [--min-days=30]` | Exit 1 when `Expires` is missing, invalid or closer than 30 days |
| `renew` | Move `Expires` 11 months ahead; commit the result |
| `hook` | Install the pre-push hook |

`--file=path` changes the location for any command (default `public/.well-known/security.txt`).

## CI

Copy [`ci/gitlab.yml`](ci/gitlab.yml) into `.gitlab-ci.yml` or [`ci/github.yml`](ci/github.yml) into `.github/workflows/`. Both run `vendor/bin/security-txt check` on merge requests and the default branch; GitHub also every Monday, so an expiring file fails before anyone pushes.
