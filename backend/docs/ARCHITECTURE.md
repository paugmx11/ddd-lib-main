# Arquitectura (Backend)

Aquest backend segueix una estructura DDD (Application / Domain / Infrastructure) i exposa una API REST sota `/api/*`.

## Routing

- `backend/index.php` detecta si la ruta comença per `/api` i delega a `ApiRouter`.
- Les rutes de l’API no estan hardcodejades al router: estan definides a `backend/config/api_routes.php`.
- `backend/src/Infrastructure/Web/Router/ApiRouter.php` fa el “match” de ruta i crida un controlador concret.

## Controllers

Per evitar un `ApiController` “god mode”, cada recurs té el seu controlador REST:

- `backend/src/Infrastructure/Web/Controller/Api/StudentApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/TeacherApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/SubjectApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/CourseApiController.php`
- `backend/src/Infrastructure/Web/Controller/Api/AuthApiController.php`

## Autenticació

- Endpoints públics: `/api/auth/*`
- Resta de endpoints: requereixen `Authorization: Bearer <token>`
- Validació del token: `backend/src/Infrastructure/Web/Router/ApiRouter.php` (via `UserTokenRepository`)

