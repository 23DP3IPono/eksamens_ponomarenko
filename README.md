# Ceļojumu plānotāja sistēma

Tiešsaistes sistēma ceļojumu plānošanai, kas ļauj lietotājiem ērti pārvaldīt savus braucienus, galamērķus, apskates objektus, rezervācijas un izdevumus vienuviet.

Atšķirībā no tipiskām ceļojumu lapām, kas koncentrējas tikai uz biļešu vai viesnīcu rezervēšanu, šī sistēma piedāvā pilnvērtīgu personalizēta ceļojuma plāna izveidi — no maršruta līdz budžetam.

## Tehnoloģijas

- **Frontend**: Vue.js 3, Vuetify, Vue Router, Pinia, Chart.js
- **Backend**: Laravel 11, Sanctum (autentifikācija)
- **Datu bāze**: SQLite (izstrādei) / MySQL (produkcijā)
- **Versiju kontrole**: Git & GitHub

## Projekta struktūra

- `backend/` — Laravel REST API
- `frontend/` — Vue.js SPA ar Vuetify komponentēm

## Funkcionalitāte

### Lietotāju autentifikācija
- Reģistrēšanās un pieslēgšanās ar e-pastu un paroli
- Token-based autentifikācija (Laravel Sanctum)
- Divas lietotāju lomas: `Viesis` un `Registrets`
- Formu validācija klienta un servera pusē

### Ceļojumu pārvaldība
- Jauna ceļojuma izveide ar nosaukumu, galamērķi, datumiem un budžetu
- Ceļojumu rediģēšana un dzēšana (tikai īpašnieks)
- "Mani ceļojumi" lapa — reģistrētā lietotāja personīgie plāni

### Detalizēta plānošana katrā ceļojumā
- **Maršruts** (dienas punkti) — vietas ar datumiem un aprakstiem
- **Rezervācijas** — aviobiletes, viesnīcas, citi pakalpojumi
- **Izdevumi** — kategorizēti izdevumi ar summām un datumiem
- **Vietas** — esošu vietu izvēle vai jaunu pievienošana

### Datu meklēšana un atlase
- Meklēšana pēc ceļojuma nosaukuma vai galamērķa
- Paplašināta filtrēšana — budžeta intervāls, datuma intervāls
- Kārtošana pēc vairākiem laukiem (augoši / dilstoši)

### Statistika un vizualizācija
- Kopsavilkuma kartītes (ceļojumu skaits, kopējais budžets, izdevumi u.c.)
- Stabiņu diagramma — ceļojumi pa galamērķiem
- Riņķa diagramma — izdevumi pa kategorijām
- Detalizētas tabulas

### Datu modelēšana
- 6 savstarpēji saistītas tabulas (`users`, `celojums`, `vieta`, `dienas_punkts`, `rezervacija`, `izdevums`)
- JOIN vaicājumi datu apvienošanai no vairākām tabulām
- Agregāciju vaicājumi (SUM, COUNT, AVG, GROUP BY)

## Kā palaist projektu lokāli

### Priekšnosacījumi
- PHP 8.2 vai jaunāks
- Composer
- Node.js 18 vai jaunāks
- npm

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Pēc noklusējuma backend palaiž vietnē `http://127.0.0.1:8000`.

### Frontend
```bash
cd frontend
npm install
npm run dev
```

Pēc noklusējuma frontend palaiž vietnē `http://localhost:5173`.


## API pārskats

Publiski pieejamās galapunktes:
- `GET /api/celojumi` — ceļojumu saraksts (ar meklēšanu, filtriem, kārtošanu)
- `GET /api/celojumi/{id}` — viena ceļojuma detalizēti dati
- `GET /api/celojumi/stats` — statistikas dati
- `GET /api/vietas` — vietu saraksts
- `POST /api/register` — jauna lietotāja reģistrācija
- `POST /api/login` — pieslēgšanās

Autentificētās galapunktes (nepieciešams `Authorization: Bearer <token>`):
- `POST|PUT|DELETE /api/celojumi` — CRUD ceļojumiem
- `POST|PUT|DELETE /api/izdevumi` — CRUD izdevumiem
- `POST|PUT|DELETE /api/rezervacijas` — CRUD rezervācijām
- `POST|PUT|DELETE /api/dienas-punkti` — CRUD dienas punktiem
- `POST /api/vietas` — jaunu vietu izveide
- `POST /api/logout` — atteikšanās
- `GET /api/me` — informācija par pašreizējo lietotāju

## Autors

Ivans Ponomarenko  
Rīgas Valsts tehnikums, Datorikas nodaļa  
Izglītības programma: Programmēšana  
2025./2026. m.g.