# Laravel Ip Whitelist

This package is intended for ip whitelist feature in laravel project. It will help you to check if the user is coming from the whitelisted ip or not.
by configuring the whitelist ip in the config file.

---

## Requirement

- PHP >= 8.0
- Laravel >= 8.0

## Getting Started

### Package installation

```bash
composer require jhonoryza/laravel-ipwhitelist
```

adjust `.env` file like this

```env
IP_WHITELIST=127.0.0.1,192.168.1.1
```

register the middleware `ipwhitelist` in route or controller

---

## Security

If you've found a bug regarding security, please mail [jardik.oryza@gmail.com](mailto:jardik.oryza@gmail.com) instead of
using the issue tracker.

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
