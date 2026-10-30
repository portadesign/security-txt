# Security Policy

## Reporting a vulnerability

Do not open a public issue. Report to security@portadesign.cz; we confirm within 2 business days.

## Supported versions

Only the `main` branch and the currently deployed production release receive fixes.

## Known advisories

Pimcore 11 advisories are backported by `portadesign/pimcore-11-security-patches`
(https://github.com/portadesign/pimcore-11-security-patches). Accepted or mitigated advisories are
listed in `composer.json` (`config.audit.ignore`) and in the package's `vex.openvex.json`.

## Secrets

No secret lives in Git. Pimcore YAML configs (Data Hub, custom reports) reference `%env(NAME)%`;
values come from `.env.local` on each server. `.env` holds placeholders only.
