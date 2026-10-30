# pimcore-security-template

Drop-in security baseline for portadesign Pimcore 11 projects.

```sh
./apply.sh ../my-project pim.example.com
```

Writes (existing files are skipped, `FORCE=1` overwrites):

| File | Purpose |
| --- | --- |
| `public/.well-known/security.txt` | RFC 9116 contact, `Expires` set to +11 months |
| `SECURITY.md` | Project policy, replaces the Pimcore skeleton default |
| `config/packages/security_hardening.yaml` | Data Hub and custom reports read-only (`write_target: disabled`), so admin saves cannot overwrite `%env()%` |
| `trivy.yaml`, `.trivyignore.yaml` | Trivy reads the VEX shipped by `pimcore-11-security-patches` |
| `.betterleaksignore` | SHA-256 allowlist for sample values that must stay in Git |
| `bin/security-txt-check.php` | Fails when `Expires` is < 30 days away; `bin/security-txt-renew.sh` bumps it |
| `.githooks/pre-push` | Runs the check locally (`core.hooksPath` is set by `apply.sh`) |
| `.gitlab/security-check.yml`, `.github/workflows/security-check.yml` | Check + `composer audit` in CI |

Follow the "Next" steps `apply.sh` prints; they need project-specific decisions (secrets, CI include).
