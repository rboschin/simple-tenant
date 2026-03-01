# SimpleTenant for Laravel

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

SimpleTenant is a lightweight multi-tenancy package for Laravel that allows you to identify tenants based on the HTTP domain, URL path, or a single-tenant configuration. It handles tenant scoping automatically using global scopes and traits.

## Features

- 🌐 **Domain Identification**: Resolves tenants by host (e.g., `domain.com` or `sub.domain.com`). Includes automatic `www.` normalization.
- 🛣️ **Path Identification**: Resolves tenants by the first URL segment (e.g., `domain.com/tenant-slug`).
- 👤 **Single-Tenant Mode**: Override all identification and force a specific tenant UUID via `.env`.
- 🛡️ **Automatic Scoping**: Use the `BelongsToTenant` trait to automatically filter queries and set tenant IDs on creation.
- 💾 **Session-Based**: Current tenant context (UUID, name, etc.) is stored in the session for security and performance.
- 🔢 **Hybrid IDs**: Tenants use both auto-incrementing integers for internal use and UUIDs for public/application references.

## Installation

### Via GitHub (Remote)

If the package is hosted on GitHub:

```bash
composer require rboschin/simple-tenant
```

### Via Local Repository

To use the package during development or from a local folder, add the following to your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../packages/simpletenant"
        }
    ],
    "require": {
        "rboschin/simple-tenant": "dev-main"
    }
}
```

Then run:

```bash
composer update
```

## Setup

SimpleTenant is designed to work out-of-the-box with minimal configuration.

### 1. Automatic Configuration

When you install the package, the following are handled automatically via the Service Provider:

- **Migrations**: Core tables (`tenants`, `tenant_domains`, `tenant_paths`) are automatically loaded. Just run `php artisan migrate`.
- **Middleware**: The `IdentifyTenant` middleware is automatically pushed to the `web` middleware group. No manual registration is required.
- **Config**: Default configuration is merged automatically.

### 2. Publishing Seeders (Recommended)

To set up your initial tenant data, you only need to run one command to publish the seeders:

```bash
php artisan simpletenant:publish-seeds
```

This will create `database/seeders/TenantSeeder.php` in your application. You can then run:

```bash
php artisan db:seed --class=Database\\Seeders\\TenantSeeder
```

### 3. Manual Assets (Optional)

If you wish to customize the configuration or migrations, you can still publish them:

```bash
# To customize configuration (config/simpletenant.php)
php artisan vendor:publish --tag=simpletenant-config

# To customize core migrations
php artisan vendor:publish --tag=simpletenant-migrations
```

---

## Usage

### Preparing Models

Add the `BelongsToTenant` trait to any model that should be scoped by tenant.

```php
namespace App\Models;

use Rboschin\SimpleTenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToTenant;
}
```

- **Filtering**: All queries (e.g., `Product::all()`) will automatically include `WHERE tenant_uuid = '...'`.
- **Creation**: The `tenant_uuid` will be automatically set to the current tenant's UUID upon saving.

### Accessing Current Tenant

Use the `SimpleTenant` facade to access the current context:

```php
use Rboschin\SimpleTenant\Facades\SimpleTenant;

$uuid = SimpleTenant::uuid();
$name = SimpleTenant::name();
$tenant = SimpleTenant::tenant(); // Returns the Tenant model instance
```

### Single-Tenant Mode

To force the application to use a single tenant (ignoring domain/path), add the following to your `.env`:

```env
SIMPLETENANT_SINGLE_TENANT_UUID=550e8400-e29b-41d4-a716-446655440000
```

## Configuration

The `config/simpletenant.php` file allows you to:
- Define `central_domains` that should be ignored by the identify middleware.
- Customize the `tenant_model`.
- Toggle domain or path resolution.
- Change the `session_key` or `tenant_uuid_column` name.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Author

Roberto Boschin ([rboschin@gmail.com](mailto:rboschin@gmail.com))
