# Monorepo

Projecte de serveis web / apps híbrides.

El repositori està separat en:

- `backend/`: API REST (PHP + Doctrine)
- `frontend/`: client SPA (Laravel)

## Requisits

- PHP (backend i frontend)
- Composer
- Node + npm (només per compilar assets del frontend amb Vite)

## Backend

API REST + autenticació.

- Documentació: `backend/README.md`
- Proves/colecció API (Postman): `backend/docs/api/school-api.postman_collection.json`
- Notes de tests i comprovacions: `backend/docs/api/PROVES_ENDPOINTS.md`

Execució ràpida:

```bash
cd backend
composer install
php -S 127.0.0.1:8000 index.php
```

Endpoints principals (exemples):

- `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`
- `GET/POST /api/students`, `GET/PUT/DELETE /api/students/{id}`, `POST /api/students/{id}/enroll`
- `GET/POST /api/teachers`, `GET/PUT/DELETE /api/teachers/{id}`, `POST /api/teachers/{id}/assign`, `POST /api/teachers/{id}/unassign`
- `GET/POST /api/subjects`, `GET/PUT/DELETE /api/subjects/{id}`

## Frontend

Client SPA fet amb Laravel.

Idea: el navegador parla amb el frontend (`/client-api/*`) i Laravel fa de “proxy” cap al backend (evita CORS).

- Documentació: `frontend/README.md`
- Notes de proves client: `frontend/docs/PROVES_CLIENT.md`

Execució ràpida (en un altre terminal, amb el backend ja arrencat):

```bash
cd frontend
composer install
npm install
npm run build
php artisan serve --port=8001
```

Flux d’ús:

1) Registra’t o fes login a `http://127.0.0.1:8001/register` / `http://127.0.0.1:8001/login`
2) Entra al dashboard a `http://127.0.0.1:8001/dashboard`
3) Navega a `/students`, `/teachers`, `/subjects` (SPA, sense `#`)

Configuració important:

- `frontend/.env` defineix `BACKEND_BASE_URL` (per defecte `http://127.0.0.1:8000`)

## Tests

- Backend: `cd backend && ./vendor/bin/phpunit`
- Frontend: `cd frontend && php artisan test`
