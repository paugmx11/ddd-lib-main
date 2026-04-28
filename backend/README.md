# School Management DDD (API REST)

Projecte d'escola per practicar una API RESTful (PHP + Doctrine ORM + SQLite).

## Requisits

- PHP 8.2+ (al repo s'ha provat amb PHP 8.4)
- Composer (dependències ja estan al directori `vendor/`)

## Configuracio

L'arxiu `.env` defineix la base de dades:

- `DATABASE_URL="sqlite:///.../database.sqlite"`

## Executar el servidor (dev)

```bash
php -S localhost:8000 index.php
```

API base URL: `http://localhost:8000`

## Autenticacio

Tots els endpoints `GET/POST/PUT/PATCH/DELETE` sota `/api/*` requereixen `Authorization: Bearer <token>`,
excepte `/api/auth/*`.

Per obtenir un token:

- `POST /api/auth/register`
- o `POST /api/auth/login`

## Arquitectura (resum)

- Router API: `src/Infrastructure/Web/Router/ApiRouter.php` + rutes a `config/api_routes.php`
- Controladors REST (un per recurs): `src/Infrastructure/Web/Controller/Api/*ApiController.php`
- Application/Domain/Infrastructure (DDD): `src/Application`, `src/Domain`, `src/Infrastructure`

## Tests d'endpoint (Postman/Apidog)

- Col·leccio: `docs/api/school-api.postman_collection.json`
- Environment: `docs/api/school-api.postman_environment.json`
- Guia: `docs/api/PROVES_ENDPOINTS.md`

## Tests de codi (PHPUnit)

```bash
./vendor/bin/phpunit
```
