# Proves de funcionament (Client SPA)

Aquesta guia serveix per generar evidencies (captures) de que el client Laravel (SPA) consumeix la API del `backend/`.

## 1) Arrencar serveis

Backend:

```bash
cd backend
php -S 127.0.0.1:8000 index.php
```

Frontend:

```bash
cd frontend
npm install
npm run dev
php artisan serve --port=8001
```

URL: `http://127.0.0.1:8001`

## 2) Autenticacio

Opcio A (Register):

1. Entra a `/register`
2. Crea usuari (Laravel login)
3. Redirigeix a la SPA (`/` i `#/dashboard`)

Opcio B (Login):

1. Entra a `/login`
2. Inicia sessio (Laravel login)
3. Redirigeix a la SPA (`/` i `#/dashboard`)

## 3) Flux de dades (minim)

1. `Courses`: crea un curs (queda a la taula)
2. `Subjects`: crea un subject seleccionant el course creat
3. `Teachers`: crea un teacher i fes `Assign` al subject
4. `Students`: crea un student i fes `Enroll` al course

## 4) Evidencies per entregar

Com a minim:

1. Captura del `#/dashboard` amb badge `Authenticated`
2. Captura de `#/courses` mostrant el curs creat
3. Captura de `#/subjects` mostrant el subject creat (i `teacherId` assignat si aplica)
4. Captura de `#/teachers` amb el teacher creat
5. Captura de `#/students` amb el student creat
