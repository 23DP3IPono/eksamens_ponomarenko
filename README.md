# Frizētavas rezervācijas sistēma

Šis projekts ir frizētavas pakalpojumu rezervācijas sistēmas prototips.
Frontend izstrādāts ar Vue.js un Vuetify, backend - ar Laravel.

## Tehnoloģijas
- Vue.js
- Vuetify
- Laravel
- MySQL
- Git & GitHub

## Projekta struktūra
- `frontend/` – Vue.js aplikācija (Home page)
- `backend/` – Laravel API (services dati)

## Kā palaist projektu
1. Pārliecinies, ka ir instalēts PHP, Composer, Node.js un MySQL.
2. Backend:
   - `cd backend`
   - `composer install`
   - `.env` konfigurācija (DB dati)
   - `php artisan migrate`
   - `php artisan serve`
3. Frontend:
   - `cd frontend`
   - `npm install`
   - `npm run dev`

## Funkcionalitāte (minimālā)
- Home page ar pakalpojumu sarakstu no datubāzes
- API, kas atgriež pakalpojumu datus
- Datu apmaiņa starp frontend un backend
