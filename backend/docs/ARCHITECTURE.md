# Arquitectura (Backend)

Aquest backend segueix una estructura DDD (Application / Domain / Infrastructure) i exposa una API REST sota `/api/*`.

## Routing

- `backend/index.php` detecta si la ruta comença per `/api` i delega a `ApiRouter`.
- Les rutes de l’API no estan hardcodejades al router: estan definides a `backend/config/api_routes.php` (amb un flag opcional per marcar rutes públiques).
- `backend/src/Infrastructure/Web/Router/ApiRouter.php` fa el “match” de ruta i crida un controlador concret.

## Controllers

Per evitar un `ApiController` “god mode”, cada recurs té el seu controlador REST:

- `backend/src/Infrastructure/Web/Controller/Api/StudentApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/TeacherApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/SubjectApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/CourseApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/AuthApiController.php`

## Autenticació

- Endpoints públics: `POST /api/auth/register` i `POST /api/auth/login`
- Resta de endpoints: requereixen `Authorization: Bearer <token>`
- Validació del token:
  - Extractor: `backend/src/Infrastructure/Web/Auth/BearerTokenExtractor.php`
  - Autenticador: `backend/src/Infrastructure/Web/Auth/UserTokenAuthenticator.php`
  - Aplicació per ruta: `backend/src/Infrastructure/Web/Router/ApiRouter.php` (segons `backend/config/api_routes.php`)
