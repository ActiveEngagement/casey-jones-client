---
"casey-jones": minor
---

Support spatie/typescript-transformer v3. The `@typescript` docblock declarations on the Data classes and enums are ignored by v3, so they are migrated to `#[TypeScript]` attributes (custom type names preserved via `#[TypeScript(name: '...')]`).
