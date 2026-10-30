# pimcore-security-template

security.txt (RFC 9116) with expiry checks for portadesign Pimcore projects.

```sh
./apply.sh ../my-project pim.example.com
```

Writes (existing files are skipped, `FORCE=1` overwrites):

| File | Purpose |
| --- | --- |
| `public/.well-known/security.txt` | Contact security@portadesign.cz, `Expires` set to +11 months, `Canonical` with the given host |
| `bin/security-txt-check.php [days]` | Exits 1 when `Expires` is missing, invalid or < 30 days away |
| `bin/security-txt-renew.sh` | Bumps `Expires` by 11 months |
| `.githooks/pre-push` | Runs the check locally (`core.hooksPath` is set by `apply.sh`) |
| `.gitlab/security-check.yml`, `.github/workflows/security-check.yml` | Check + `composer audit --locked --no-dev` in CI, weekly on GitHub |

Scanner silencing (Composer audit ignore, Trivy VEX) is documented in [pimcore-11-security-patches](https://github.com/portadesign/pimcore-11-security-patches#silencing-scanners).
