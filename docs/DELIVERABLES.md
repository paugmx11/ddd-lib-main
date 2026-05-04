# Entregables (Projecte Escola)

Data d’avui: 2026-05-04.

## 1a Part — API Backend (avaluació 2026-04-16, 20%)

### Què s’ha d’entregar

- Enllaç al repositori de GitHub amb commits per feature (mínim):
  - `feat(api)` routing manual
  - `feat(students)` endpoints students
  - `feat(teachers)` endpoints teachers
  - `feat(subjects)` endpoints subjects
- Document/enllaç amb evidències de proves dels endpoints (Postman/Apidog).

### On és al repo

- Backend: `backend/`
- Rutes API: `backend/config/api_routes.php`
- Router: `backend/src/Infrastructure/Web/Router/ApiRouter.php`
- Docs proves API: `backend/docs/api/PROVES_ENDPOINTS.md`
- Col·lecció Postman: `backend/docs/api/school-api.postman_collection.json`
- Environment Postman: `backend/docs/api/school-api.postman_environment.json`
- Notes (doc extra): `docs/api-backend-tests.md`

### Com executar (mínim)

```bash
cd backend
composer install
php -S localhost:8000 index.php
```

### Autenticació (API)

- Públic: `POST /api/auth/register`, `POST /api/auth/login`
- Privat: la resta de `/api/*` (inclòs `POST /api/auth/logout`)
- Header: `Authorization: Bearer <token>`

## 2a Part — Client del servei (avaluació/tramesa 2026-05-06, 20%)

### Què s’ha de valorar

- Enllaç a GitHub amb commits per feature (mínim):
  - accés a `teachers`, `students`, `subjects`
  - autenticació al client
- Document/enllaç amb evidències de funcionament.
- Interconnexió real client <-> API.

### On és al repo

- Frontend Laravel: `frontend/`
- Docs proves client: `frontend/docs/PROVES_CLIENT.md`
- Config backend base URL: `frontend/.env` (`BACKEND_BASE_URL`)

### Com executar (mínim)

Terminal 1 (backend):

```bash
cd backend
php -S 127.0.0.1:8000 index.php
```

Terminal 2 (frontend):

```bash
cd frontend
composer install
php artisan serve --port=8001
```

URL: `http://127.0.0.1:8001`

