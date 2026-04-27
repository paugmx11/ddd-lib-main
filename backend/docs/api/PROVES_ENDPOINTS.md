# Proves de funcionament (Endpoints API)

Aquesta guia serveix per generar evidencies (captures o export) del funcionament dels endpoints REST amb Postman o Apidog.

## 1) Arrencar el backend

```bash
php -S localhost:8000 index.php
```

## 2) Importar col·leccio i environment

Importa aquests fitxers:

- Col·leccio: `docs/api/school-api.postman_collection.json`
- Environment: `docs/api/school-api.postman_environment.json`

Selecciona l'environment `School API (local)` i comprova:

- `baseUrl` = `http://localhost:8000`

## 3) Executar les proves

Executa la col·leccio en ordre (Runner):

1. Auth - Register (sets token)
2. Courses - Create (sets courseId)
3. Subjects - Create (sets subjectId)
4. Teachers - Create (sets teacherId)
5. Teachers - Assign subject
6. Students - Create (sets studentId)
7. Students - Enroll in course
8. CRUD (GET/PUT) de cada recurs
9. Cleanup (DELETEs + logout)

Els requests inclouen tests que validen:

- codis d'estat (200/201/204/4xx)
- presence de camps basics
- guardat de variables (`token`, `courseId`, `subjectId`, `teacherId`, `studentId`)

## 4) Evidencies per entregar

Opcions valides (trieu una):

- Captures del Runner amb tots els requests en verd (pass).
- Export del report del Runner (si el client ho permet).
- Enllac a Apidog amb el resultat de les proves.

Inclou com a minim:

- captura del `Auth - Register` (token rebut)
- captura d'un `GET /api/teachers` amb 200
- captura d'un `GET /api/students` amb 200
- captura d'un `GET /api/subjects` amb 200
