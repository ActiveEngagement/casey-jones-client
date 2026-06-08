---
"casey-jones": major
---

Laravel 13 support and project modernization.

- **Breaking:** require PHP 8.3+ and Laravel 13; drop support for Laravel 11 and 12.
- Switch release tooling from semantic-release to Changesets.
- CI runs `pest --coverage --min=100` across PHP 8.3/8.4/8.5 and Laravel 13, plus Pint, PHPStan (level max), and Rector.
- Add the `Client::campaigns()` resource (`CampaignResource`).
- Add `--poll` and `--timeout` options to the `casey:listen` command.
- Support spatie/typescript-transformer v3 by migrating the `@typescript` docblock declarations to `#[TypeScript]` attributes.
- Ship generated TypeScript definitions at `resources/types/casey-jones.d.ts` (regenerate with `composer types`).
