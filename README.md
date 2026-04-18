# Aurateria HQ

Internal **operations and workforce management** platform: finance, tasks, grocery workflows, attendance, holidays, and daily reporting. Built with **Laravel 12**, **Blade + Tailwind**, **Vite**, and **Laravel Sanctum** for a token-based JSON API. Authorization uses **Spatie Laravel Permission** (roles and fine-grained permissions).

See [`PROJECT_CONTEXT.md`](PROJECT_CONTEXT.md) for architecture, request flow, and domain details.

## Requirements

- PHP **8.2+**
- Composer
- Node.js **18+** (for Vite / front-end build)
- MySQL (or adapt `.env` for your database)

## Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build   # or npm run dev during development
```

Configure database and mail in `.env`. For API features that use AI, set `OPENAI_API_KEY` if needed.

## Tests

```bash
php artisan test
```

## License

This project is licensed under the MIT License — see [LICENSE](LICENSE).
