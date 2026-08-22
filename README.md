# Support

**Shared helpers used across the Innobox R&R Laravel packages.**

This is the utility layer the other packages depend on — HTTP handling, request formatting, phone normalisation and a small data container. It is published because the packages need it, not because it is a product.

## What is inside

| Component | Purpose |
|---|---|
| `Helpers/HttpHelper` | Outbound HTTP calls with consistent error handling |
| `Helpers/RequestHelper` | Reading and normalising incoming request data |
| `Http/Requests/RequestFormater` | A shared base for form requests across packages |
| `Jobs/DispatchJob` | Generic queued dispatch used by dependent packages |
| `Utils/DataContainer` | A simple typed container for passing structured data around |
| `Utils/PhoneFormatter` | Phone number normalisation, Mexico-aware |

It also registers the standard service providers (`App`, `Auth`, `Event`, `Route`) that the dependent packages hook into.

## Should you install this directly?

Usually not. It arrives as a dependency of the package you actually wanted — [`deals`](https://github.com/innoboxrr/deals), [`consultant-manager`](https://github.com/innoboxrr/consultant-manager), [`omni-billing`](https://github.com/innoboxrr/omni-billing) and others. Install it on its own only if you want `PhoneFormatter` or `DataContainer` in isolation.

```bash
composer require innoboxrr/support
php artisan vendor:publish --tag=innoboxrr-support-config
```

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
