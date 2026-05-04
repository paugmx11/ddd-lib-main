# Casos d’ús (Backend escola)

Aquest projecte implementa els casos d’ús mínims demanats per l’API/vistes del projecte d’escola:

1. `CreateStudent`
2. `CreateCourse`
3. `CreateSubject`
4. `CreateTeacher`
5. `EnrollStudent`
6. `AssignTeacherToSubject`

## On són (capa d’aplicació)

- `backend/src/Application/CreateStudent/`
- `backend/src/Application/CreateCourse/`
- `backend/src/Application/CreateSubject/`
- `backend/src/Application/CreateTeacher/`
- `backend/src/Application/EnrollStudent/`
- `backend/src/Application/AssignTeacherToSubject/`

## Tests (PHPUnit)

- Cas d’ús 5 (`EnrollStudent`): `backend/tests/Application/EnrollStudentTest.php`
- Cas d’ús 6 (`AssignTeacherToSubject`): `backend/tests/Application/AssignTeacherToSubjectTest.php`
- Tests de domini (entitats/valors): `backend/tests/Domain/*`

## Vistes i controladors associats

- Web (HTML): `backend/src/Infrastructure/Web/Controller/*Controller.php` + `backend/views/`
- API (JSON): `backend/src/Infrastructure/Web/Controller/Api/*ApiController.php`

## Notes

El projecte també inclou autenticació per la API (`RegisterUser` / `LoginUser`) per poder consumir endpoints protegits amb `Authorization: Bearer <token>`.

