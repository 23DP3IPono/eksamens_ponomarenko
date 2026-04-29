<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Celojums;
use App\Models\Vieta;
use App\Models\DienasPunkts;
use App\Models\Rezervacija;
use App\Models\Izdevums;

class TripPlannerSeeder extends Seeder
{
    public function run(): void
    {
        // === USERS ===
        User::create([
            'name' => 'Admins',
            'uzvards' => 'Sistēmas',
            'email' => 'admin@celojumi.lv',
            'password' => Hash::make('admin123'),
            'loma' => 'Admins',
        ]);

        $user1 = User::create([
            'name' => 'Jānis',
            'uzvards' => 'Bērziņš',
            'email' => 'janis@test.lv',
            'password' => Hash::make('parole123'),
            'loma' => 'Registrets',
        ]);

        $user2 = User::create([
            'name' => 'Anna',
            'uzvards' => 'Kalniņa',
            'email' => 'anna@test.lv',
            'password' => Hash::make('parole123'),
            'loma' => 'Registrets',
        ]);

        // === PLACES ===
        $riga = Vieta::create([
            'nosaukums' => 'Vecrīga',
            'adrese' => 'Rātslaukums 1, Rīga',
            'koordinatas' => '56.9475, 24.1069',
            'tips' => 'Apskates objekts',
        ]);

        $jurmala = Vieta::create([
            'nosaukums' => 'Jūrmalas pludmale',
            'adrese' => 'Jomas iela, Jūrmala',
            'koordinatas' => '56.9680, 23.7704',
            'tips' => 'Pludmale',
        ]);

        $sigulda = Vieta::create([
            'nosaukums' => 'Siguldas pilsdrupas',
            'adrese' => 'Pils iela 16, Sigulda',
            'koordinatas' => '57.1539, 24.8531',
            'tips' => 'Pilsdrupas',
        ]);

        $gauja = Vieta::create([
            'nosaukums' => 'Gaujas Nacionālais parks',
            'adrese' => 'Sigulda',
            'koordinatas' => '57.1641, 24.8595',
            'tips' => 'Dabas objekts',
        ]);

        $viesnica = Vieta::create([
            'nosaukums' => 'Hotel Latvia',
            'adrese' => 'Elizabetes iela 55, Rīga',
            'koordinatas' => '56.9563, 24.1162',
            'tips' => 'Viesnīca',
        ]);

        // === TRIPS ===
        $trip1 = Celojums::create([
            'nosaukums' => 'Nedēļa Latvijā',
            'galamerkis' => 'Latvija',
            'sakuma_datums' => '2026-05-10',
            'beigu_datums' => '2026-05-17',
            'budzets' => 500.00,
            'lietotajs_id' => $user1->id,
        ]);

        $trip2 = Celojums::create([
            'nosaukums' => 'Jūrmalas atpūta',
            'galamerkis' => 'Jūrmala',
            'sakuma_datums' => '2026-06-01',
            'beigu_datums' => '2026-06-05',
            'budzets' => 250.00,
            'lietotajs_id' => $user1->id,
        ]);

        $trip3 = Celojums::create([
            'nosaukums' => 'Siguldas piedzīvojums',
            'galamerkis' => 'Sigulda',
            'sakuma_datums' => '2026-07-15',
            'beigu_datums' => '2026-07-18',
            'budzets' => 300.00,
            'lietotajs_id' => $user2->id,
        ]);

        // === DAY POINTS ===
        DienasPunkts::create([
            'datums' => '2026-05-10',
            'apraksts' => 'Pastaiga pa Vecrīgu',
            'celojuma_id' => $trip1->celojuma_id,
            'vieta_id' => $riga->vieta_id,
        ]);

        DienasPunkts::create([
            'datums' => '2026-05-12',
            'apraksts' => 'Izbrauciens uz Jūrmalu',
            'celojuma_id' => $trip1->celojuma_id,
            'vieta_id' => $jurmala->vieta_id,
        ]);

        DienasPunkts::create([
            'datums' => '2026-06-01',
            'apraksts' => 'Atpūta pludmalē',
            'celojuma_id' => $trip2->celojuma_id,
            'vieta_id' => $jurmala->vieta_id,
        ]);

        DienasPunkts::create([
            'datums' => '2026-07-15',
            'apraksts' => 'Pilsdrupu apmeklēšana',
            'celojuma_id' => $trip3->celojuma_id,
            'vieta_id' => $sigulda->vieta_id,
        ]);

        DienasPunkts::create([
            'datums' => '2026-07-16',
            'apraksts' => 'Pārgājiens pa Gauju',
            'celojuma_id' => $trip3->celojuma_id,
            'vieta_id' => $gauja->vieta_id,
        ]);

        // === RESERVATIONS ===
        Rezervacija::create([
            'tips' => 'Viesnīca',
            'pakalpojuma_nosaukums' => 'Hotel Latvia - 2 naktis',
            'cena' => 120.00,
            'celojuma_id' => $trip1->celojuma_id,
        ]);

        Rezervacija::create([
            'tips' => 'Aviobilete',
            'pakalpojuma_nosaukums' => 'airBaltic Rīga-Jūrmala',
            'cena' => 45.00,
            'celojuma_id' => $trip2->celojuma_id,
        ]);

        Rezervacija::create([
            'tips' => 'Viesnīca',
            'pakalpojuma_nosaukums' => 'Sigulda Hotel - 3 naktis',
            'cena' => 180.00,
            'celojuma_id' => $trip3->celojuma_id,
        ]);

        // === EXPENSES ===
        Izdevums::create([
            'summa' => 45.50,
            'datums' => '2026-05-10',
            'kategorija' => 'Ēdiens',
            'celojuma_id' => $trip1->celojuma_id,
        ]);

        Izdevums::create([
            'summa' => 25.00,
            'datums' => '2026-05-11',
            'kategorija' => 'Transports',
            'celojuma_id' => $trip1->celojuma_id,
        ]);

        Izdevums::create([
            'summa' => 80.00,
            'datums' => '2026-06-02',
            'kategorija' => 'Izklaide',
            'celojuma_id' => $trip2->celojuma_id,
        ]);

        Izdevums::create([
            'summa' => 30.00,
            'datums' => '2026-07-15',
            'kategorija' => 'Ēdiens',
            'celojuma_id' => $trip3->celojuma_id,
        ]);

        Izdevums::create([
            'summa' => 15.00,
            'datums' => '2026-07-16',
            'kategorija' => 'Biļetes',
            'celojuma_id' => $trip3->celojuma_id,
        ]);
    }
}