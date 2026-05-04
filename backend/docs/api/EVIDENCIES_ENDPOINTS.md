# Evidències de proves (Endpoints API)

Aquest document és la “memòria” d’evidències per entregar: captures o export del funcionament dels endpoints REST definits.

> Guia d’execució (ordre i setup): `backend/docs/api/PROVES_ENDPOINTS.md`

## Setup

1) Arrenca el backend:

```bash
cd backend
php -S localhost:8000 index.php
```

> Nota: per aquestes evidències (endpoints), **el frontend no és necessari**. Les proves es fan amb Postman/Apidog contra el backend.

## (Opcional) Arrencar el frontend (Laravel client)

Si vols arrencar també el client (2a part), fes-ho en un altre terminal:

```bash
cd frontend
composer install
php artisan serve --port=8001
```

URL client: `http://127.0.0.1:8001`

2) Importa a Postman/Apidog:

- Col·lecció: `backend/docs/api/school-api.postman_collection.json`
- Environment: `backend/docs/api/school-api.postman_environment.json`

3) Verifica environment:

- `baseUrl = http://localhost:8000`

## Evidències mínimes (captures o report)

Adjunta **com a mínim** les evidències següents (captures o un report exportat equivalent):

### 1) Auth — Register (token)

- Request: `POST /api/auth/register`
- Esperat: `201`
- Evidència: resposta JSON amb `token` i `user` (id, name, email) i variable `token` guardada a l’environment.

### 2) Courses — Llistar

- Request: `GET /api/courses`
- Header: `Authorization: Bearer {{token}}`
- Esperat: `200`
- Evidència: array JSON (pot estar buit) i test en verd.

### 3) Teachers — Llistar

- Request: `GET /api/teachers`
- Header: `Authorization: Bearer {{token}}`
- Esperat: `200`
- Evidència: array JSON i test en verd.

### 4) Students — Llistar

- Request: `GET /api/students`
- Header: `Authorization: Bearer {{token}}`
- Esperat: `200`
- Evidència: array JSON i test en verd.

### 5) Subjects — Llistar

- Request: `GET /api/subjects`
- Header: `Authorization: Bearer {{token}}`
- Esperat: `200`
- Evidència: array JSON i test en verd.

### 6) Runner complet (recomanat)

- Acció: executar la col·lecció sencera en ordre (Runner).
- Esperat: tots els requests en verd (pass).
- Evidència: captura del Runner final o report exportat amb el resum (pass/fail).

## Evidències extra (si cal demostrar CRUD complet)

Si el professor demana evidència CRUD, afegeix:

- Exemple d’edició (Update):
  - `PUT /api/courses/{id}` (200) o `PUT /api/students/{id}` (200)
- `POST /api/courses` (201 + `id` o `Location`)
- `POST /api/subjects` (201 + `subjectId`)
- `POST /api/teachers` (201 + `teacherId`)
- `POST /api/teachers/{id}/assign` (200)
- `POST /api/students` (201 + `studentId`)
- `POST /api/students/{id}/enroll` (200)
- `DELETE` d’un recurs (204)
