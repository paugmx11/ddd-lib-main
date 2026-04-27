# Frontend (Laravel SPA)

Client SPA (Laravel) per consumir la nostra API del projecte DDD (`../backend`).

## Configuracio

El client parla amb el backend via un "proxy" de Laravel (evita CORS):

- SPA (browser) -> `frontend/client-api/*`
- Laravel -> `BACKEND_BASE_URL` -> `backend/index.php`

Variables:

- `BACKEND_BASE_URL` a `frontend/.env` (per defecte `http://127.0.0.1:8000`)

## Executar en local

1) Arrenca el backend:

```bash
cd backend
php -S 127.0.0.1:8000 index.php
```

2) Arrenca el frontend:

```bash
cd frontend
npm install
npm run build
php artisan serve --port=8001
```

Obre `http://127.0.0.1:8001` i autentica't amb Laravel:

- `/register` o `/login`
- Despres, SPA a `/dashboard` (o `/`) amb seccions CRUD.
