# Casey Jones

The official PHP/Laravel client for communicating with [Casey Jones](https://casey.actengage.com) — the Active Engagement sending platform. It wraps the Casey Jones REST API, the MessageGears service, a Redis-backed event stream, and the `Send`/`SendJob` Eloquent models.

- **Requires:** PHP `^8.3`, Laravel `^13.0`
- **License:** MIT

## Installation

```bash
composer require actengage/casey-jones
```

The service provider and the `Client` / `MessageGears` facades are auto-discovered.

### Publish the config

```bash
php artisan vendor:publish --tag=casey-config
```

### Publish the migrations

The package ships `sends` and `send_jobs` migrations:

```bash
php artisan vendor:publish --tag=casey-migrations
php artisan migrate
```

### Environment

```dotenv
CASEY_API_KEY=your-personal-access-token
CASEY_BASE_URI=https://casey.actengage.com/api/
CASEY_REDIS_CONNECTION=casey
```

## Usage

### The REST client

The `Client` facade talks to the Casey Jones REST API. Each resource exposes the
usual `index` / `show` / `create` / `update` / `delete` methods and returns typed
`spatie/laravel-data` objects.

```php
use Actengage\CaseyJones\Facades\Client;

// The authenticated app
Client::app();

// Instances
Client::instances()->index();
Client::instances()->show($instanceId);
Client::instances()->templates($instanceId)->index();
Client::instances()->folders($instanceId)->tree();

// Sends
Client::sends()->index();
Client::sends()->create([...]);

// Campaigns
Client::campaigns()->show($campaignId);
```

### MessageGears

The `MessageGears` facade wraps the MessageGears Accelerator API.

```php
use Actengage\CaseyJones\Facades\MessageGears;

MessageGears::getTemplate($templateId);
MessageGears::getTemplates(page: 1, limit: 50);
MessageGears::createMarketingCampaignJob($campaignId);
MessageGears::checkMarketingCampaignJobStatus($campaignId, $jobId);
MessageGears::getFolders();
MessageGears::getFolderTree();
```

### Models

`Send` and `SendJob` are Eloquent models with status scopes and casts:

```php
use Actengage\CaseyJones\Models\Send;
use Actengage\CaseyJones\Enums\SendStatus;

Send::scheduled()->readyToSend()->get();
Send::status(SendStatus::Active, SendStatus::Queued)->get();
```

### The event stream

Casey Jones publishes events to a Redis stream. Run the listener to consume them
and re-dispatch each payload as a `StreamEventReceived` event:

```bash
php artisan casey:listen
```

Register listeners for streamable events with the `StreamableEvent` facade:

```php
use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Events\StreamEventReceived;
use Actengage\CaseyJones\Facades\StreamableEvent;

StreamableEvent::listen(SendCreated::class, function (StreamEventReceived $event) {
    // $event->payload is the SendCreated instance
});

// Catch any event that has no explicit listener
StreamableEvent::catch(fn (StreamEventReceived $event) => /* ... */);
```

Scaffold a listener class:

```bash
php artisan casey:listener SendCreatedListener
```

Signal all running listeners to restart (e.g. after a deploy):

```bash
php artisan casey:restart
```

## Testing

```bash
composer test            # or: vendor/bin/pest
vendor/bin/pest --coverage --min=100
vendor/bin/pint --test   # code style
vendor/bin/phpstan analyse
vendor/bin/rector --dry-run
```

CI runs the Pest suite (with 100% coverage enforced) across PHP 8.3 / 8.4 / 8.5 and
Laravel 13, plus Pint, PHPStan (level max), and Rector.

## Releases

Releases are managed with [Changesets](https://github.com/changesets/changesets).
This is a Composer package, so "releasing" means tagging `vX.Y.Z` for Packagist —
there is no npm publish.

1. **Add a changeset** for any change that should ship:

   ```bash
   pnpm changeset
   ```

   Pick the bump level (`patch` / `minor` / `major`) and describe the change. This
   writes a markdown file under `.changeset/`.

2. **Open the release PR.** When changesets land on `master`, the `Release`
   workflow (which runs after `CI` succeeds) opens a **"Version Packages"** pull
   request that consumes the changeset files, bumps the version in `package.json`,
   and updates `CHANGELOG.md`.

3. **Merge to publish.** Merging the Version Packages PR tags the matching
   `vX.Y.Z` release and creates a GitHub Release. Packagist picks up the tag
   automatically.

> Commit messages are linted with commitlint (Conventional Commits) via a Husky
> `commit-msg` hook.
