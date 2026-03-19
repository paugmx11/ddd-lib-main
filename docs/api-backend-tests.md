# API Backend RESTFUL

Aquest document serveix com a evidència de la 1a part del projecte de serveis web.

## Recursos implementats

- `students`
- `teachers`
- `subjects`
- `courses` com a recurs de suport per poder crear `subjects` i fer `enrollments`

## Arquitectura aplicada

L’esquema segueix el model treballat a classe:

1. `index.php` llegeix `REQUEST_METHOD` i `REQUEST_URI`.
2. [src/Http/Router.php](/home/linux/projectes/ddd-lib-main%20(còpia%201)/src/Http/Router.php) aplica routing manual amb `if` i `preg_match`.
3. [src/Http/ApiController.php](/home/linux/projectes/ddd-lib-main%20(còpia%201)/src/Http/ApiController.php) transforma la petició HTTP en crides a la capa d’aplicació.
4. Els `Handlers` d’aplicació reutilitzen el DDD existent.
5. Els repositoris Doctrine persisteixen les entitats a SQLite.

## Endpoints definits

### Students

- `GET /api/students`
- `GET /api/students/{id}`
- `POST /api/students`
- `PUT /api/students/{id}`
- `DELETE /api/students/{id}`
- `POST /api/students/{id}/enrollments`

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

`POST /api/students/{studentId}/enrollments`

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
2. `feat(students): add REST endpoints for students and enrollments`
3. `feat(teachers): add REST endpoints for teachers`
4. `feat(subjects): add REST endpoints for subjects and teacher assignment`
5. `test(api): add functional API tests and documentation`
