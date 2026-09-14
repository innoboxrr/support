# Support

**Shared helpers used across the Innobox R&R Laravel packages.**

This is the utility layer the other packages depend on — HTTP handling, request formatting, phone normalisation and a small data container. It is published because the packages need it, not because it is a product.

## What is inside

| Component | Purpose |
|---|---|
| `Helpers/HttpHelper` | `getSubdomain($host, $mainDomain)`: the subdomain of a host. Without `$mainDomain` it reads `config('app.app_host')`, which Laravel doesn't define — set it or pass the domain |
| `Helpers/RequestHelper` | Reading and normalising incoming request data |
| `Http/Requests/RequestFormater` | Flattens nested form data with underscores (`seo.title` → `seo_title`) so it can be stored as flat metas; see below |
| `Jobs/DispatchJob` | Generic queued dispatch used by dependent packages |
| `Utils/DataContainer` | A simple typed container for passing structured data around |
| `Utils/PhoneFormatter` | Phone number normalisation, Mexico-aware |

It also registers the standard service providers (`App`, `Auth`, `Event`, `Route`) that the dependent packages hook into.

## Nested form data and metas

A form can send groups several levels deep. `RequestFormater` turns them into the flat keys that a model's `$editable_metas` lists, so `MetaOperations` from [`innoboxrr/traits`](https://github.com/innoboxrr/traits) can store them:

```php
use Innoboxrr\Support\Http\Requests\RequestFormater;

RequestFormater::flatten(['seo' => ['title' => 'T', 'og' => ['image' => 'x.png']]]);
// ['seo_title' => 'T', 'seo_og_image' => 'x.png']

$article->update_metas(RequestFormater::flatten($request->all()), ArticleMeta::class, 'article_id');
```

- Lists (`['a', 'b']`) and lists of `value` objects (emails, phones) are kept whole and stored as JSON.
- An empty group (`'seo' => []`) and empty values are kept, so a form can clear them: `update_metas` deletes a meta whose value is empty.
- If two keys end up the same, the first one wins.
- `RequestFormater::format($request)` replaces the request's data in place; validation rules that run afterwards must use the flattened names.

## Should you install this directly?

Usually not. It arrives as a dependency of the package you actually wanted — [`deals`](https://github.com/innoboxrr/deals), [`consultant-manager`](https://github.com/innoboxrr/consultant-manager), [`omni-billing`](https://github.com/innoboxrr/omni-billing) and others. Install it on its own only if you want `PhoneFormatter` or `DataContainer` in isolation.

```bash
composer require innoboxrr/support
php artisan vendor:publish --provider="Innoboxrr\Support\Providers\AppServiceProvider" --tag=config
```

Full documentation of the ecosystem, in Spanish and English: <https://innoboxrr.github.io/docs/paquetes/support-traits-search>.

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
