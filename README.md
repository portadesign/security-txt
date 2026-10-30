# security-template

security.txt (RFC 9116) with expiry checks for PORTA DESIGN projects (Pimcore, Symfony, anything with a web root in `public/`).

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
| `.gitlab/security-check.yml`, `.github/workflows/security-check.yml` | Runs the check on MR/PR and the default branch, weekly on GitHub |
