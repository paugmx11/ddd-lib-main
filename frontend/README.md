# Frontend (Laravel)

Client Laravel (renderitzat al servidor) per consumir la nostra API del projecte DDD (`../backend`).

## Configuracio

El client parla amb el backend via un "proxy" de Laravel (evita CORS) i via crides server-side:

- Browser -> Laravel (rutes `/dashboard`, `/students`, `/teachers`, `/subjects`)
- Laravel -> `BACKEND_BASE_URL` -> backend (`backend/index.php`)

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
php artisan serve --port=8001
```

Obre `http://127.0.0.1:8001` i autentica't amb Laravel (login/registre):

- `/register` o `/login`
- Despres, entra a `/dashboard` i ves a `/students`, `/teachers`, `/subjects`.
