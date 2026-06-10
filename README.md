#  Ceļojumu plānotājs
 
Tīmekļa lietotne ceļojumu plānošanai un budžeta uzskaitei.
 
 **Live demo:** [https://tripplanner.icu](https://tripplanner.icu)
 
---
 
##  Galvenās funkcijas
 
-  Lietotāju reģistrācija, autentifikācija ar Sanctum tokeniem un lomu sistēma (Viesis / Reģistrēts / Administrators)
-  Ceļojumu izveide
-  Maršruta plānošana ar dienas punktiem un vietām
-  Rezervāciju un izdevumu uzskaite ar dažādiem tipiem
-  Iecienīto ceļojumu sistēma
-  Meklēšana, filtri (budžets, datumi) un kārtošana
-  Statistikas lapa ar grafikiem (Bar, Pie chart) un agregācijām
-  Budžeta progresa josla ar krāsu kodējumu
-  Kontaktu forma ar ziņu saglabāšanu datu bāzē
-  Administratora panelis lietotāju, ceļojumu un ziņu pārvaldībai
-  Pielāgots dizains datoram, planšetēm un mobilajām ierīcēm
---
 
##  Tehnoloģijas
 
**Frontend:** Vue 3, Vuetify, Pinia, Vue Router, Chart.js, Vite  
**Backend:** Laravel 11, Sanctum  
**Datu bāze:** MySQL  
**Hosting:** Hostinger (Hostinger MySQL + Apache)
 
---
 
##  Datu bāzes struktūra
 
8 tabulas:
 
- `users` — lietotāji ar lomu un papildu lauku `uzvards`
- `celojums` — ceļojumi ar valsti, datumiem un budžetu
- `vieta` — vietas ar adresi, valsti un tipu
- `dienas_punkts` — dienas punkti, kas saista ceļojumu ar vietu un datumu
- `rezervacija` — rezervācijas (transports, naktsmītnes, aktivitātes)
- `izdevums` — izdevumu uzskaite pa kategorijām
- `messages` — kontaktu formas ziņas
- `favorites` — saikne starp lietotājiem un viņu iecienītajiem ceļojumiem (many-to-many)
Visas saites starp tabulām ir ar svešatslēgām un kaskādes dzēšanu, kur tas ir loģiski.
 
---
 
##  Pieejas dati testēšanai
 
| Loma | E-pasts | Parole |
|------|---------|--------|
| Lietotājs | janis@test.lv | parole123 |
| Lietotājs | anna@test.lv | parole123 |
 
---
 
##  Projekta struktūra
 
```
project/
├── backend/        # Laravel API
│   ├── app/        # Controllers, Models
│   ├── database/   # Migrations, Seeders
│   └── routes/api.php
├── frontend/       # Vue aplikācija
│   ├── src/
│   │   ├── views/      # Lapas
│   │   ├── components/ # Atkārtoti komponenti
│   │   ├── stores/     # Pinia (auth)
│   │   └── api.js      # Centralizēts API klients
│   └── public/
└── README.md
```
 
---
 
##  Autors
 
Ivans Ponomarenko  