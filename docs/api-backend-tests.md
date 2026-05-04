# API Backend RESTful

Aquest document serveix com a evidència de la 1a part del projecte de serveis web.

## Recursos implementats

- `students`
- `teachers`
- `subjects`
- `courses` com a recurs de suport per poder crear `subjects` i fer `enroll`

## Arquitectura aplicada (actual)

L’esquema segueix el model treballat a classe:

1. `backend/index.php` llegeix `REQUEST_METHOD` i `REQUEST_URI`.
2. `backend/src/Infrastructure/Web/Router/ApiRouter.php` fa el match de ruta via regex i invoca el controlador.
3. Les rutes estan definides a `backend/config/api_routes.php` (no estan hardcodejades al router).
4. Cada recurs té el seu controlador (`backend/src/Infrastructure/Web/Controller/Api/*ApiController.php`) per evitar un “god controller”.
5. Els `Handlers` d’aplicació reutilitzen el DDD existent i Doctrine persisteix a SQLite.

## Autenticació

- Públic: `POST /api/auth/register`, `POST /api/auth/login`
- Privat: la resta de `/api/*` (inclòs `POST /api/auth/logout`)
- Header: `Authorization: Bearer <token>`

## Endpoints definits

### Auth

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (requereix token)

### Students

- `GET /api/students`
- `GET /api/students/{id}`
- `POST /api/students`
- `PUT /api/students/{id}`
- `DELETE /api/students/{id}`
- `POST /api/students/{id}/enroll`

### Teachers

- `GET /api/teachers`
- `GET /api/teachers/{id}`
- `POST /api/teachers`
- `PUT /api/teachers/{id}`
- `DELETE /api/teachers/{id}`

### Subjects

- `GET /api/subjects`
- `GET /api/subjects/{id}`
- `POST /api/subjects`
- `PUT /api/subjects/{id}`
- `DELETE /api/subjects/{id}`
- `PUT /api/subjects/{id}/teacher`
- `DELETE /api/subjects/{id}/teacher`

### Courses

- `GET /api/courses`
- `POST /api/courses`

## Exemples per Postman o Apidog

Base URL:

```text
http://127.0.0.1:8000
```

### Login (obtenir token)

`POST /api/auth/login`

```json
{
  "email": "ada@example.com",
  "password": "secret"
}
```

Resposta:

```json
{
  "token": "…",
  "user": { "id": "…", "name": "…", "email": "…" }
}
```

### Crear course

`POST /api/courses`

```json
{
  "name": "DAW 2 Backend",
  "startDate": "2026-03-01",
  "endDate": "2026-06-30",
  "description": "Course for API tests"
}
```

### Crear student

`POST /api/students`

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com"
}
```

### Crear teacher

`POST /api/teachers`

```json
{
  "name": "Grace Hopper",
  "email": "grace@example.com"
}
```

### Crear subject

`POST /api/subjects`

```json
{
  "name": "Arquitectura REST",
  "courseId": "UUID_DEL_COURSE"
}
```

### Matricular student a course

`POST /api/students/{studentId}/enroll`

```json
{
  "courseId": "UUID_DEL_COURSE"
}
```

### Assignar teacher a subject

`PUT /api/subjects/{subjectId}/teacher`

```json
{
  "teacherId": "UUID_DEL_TEACHER"
}
```

## Tests funcionals automatitzats

Fitxer de test:

- [tests/Functional/ApiBackendTest.php](/home/linux/projectes/ddd-lib-main%20(còpia%201)/tests/Functional/ApiBackendTest.php)

Cobertura actual:

- Flux REST de `students`: crear, consultar, actualitzar, matricular i eliminar.
- Flux REST de `teachers` i `subjects`: crear, assignar teacher, consultar i desassignar.
- Validació d’error per JSON invàlid.

Execució:

```bash
vendor/bin/phpunit --filter ApiBackendTest
```

## Proposta de commits per a GitHub

Perquè es vegi clarament el treball per features, es recomana fer com a mínim aquests commits:

1. `feat(api): add manual router and JSON responses`
2. `feat(students): add REST endpoints for students and enroll`
3. `feat(teachers): add REST endpoints for teachers`
4. `feat(subjects): add REST endpoints for subjects and teacher assignment`
5. `test(api): add functional API tests and documentation`
