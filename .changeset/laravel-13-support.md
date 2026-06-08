---
"casey-jones": major
---

Add Laravel 13 compatibility and drop support for Laravel 11 and 12 (requires PHP 8.3+).

- CI now runs a `pest --coverage --min=100` matrix across PHP 8.3/8.4/8.5 and Laravel 13, plus an experimental channel tracking the in-development Laravel branch.
- Release tooling moved from semantic-release to Changesets.
- Added the `Client::campaigns()` resource (`CampaignResource`).
- `casey:listen` gained `--poll` and `--timeout` options.
