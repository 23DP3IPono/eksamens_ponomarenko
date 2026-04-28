<?php

namespace App\Http\Controllers;

use App\Models\Celojums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CelojumsController extends Controller
{
    public function index(Request $request)
    {
        $query = Celojums::with('lietotajs');

if ($request->boolean('mine')) {
    $user = null;
    $token = $request->bearerToken();
    if ($token) {
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if ($accessToken) {
            $user = $accessToken->tokenable;
        }
    }
    if ($user) {
        $query->where('lietotajs_id', $user->id);
    } else {
        return response()->json([]);
    }
}

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nosaukums', 'like', "%{$search}%")
                  ->orWhere('galamerkis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('budget_min')) {
            $query->where('budzets', '>=', $request->query('budget_min'));
        }
        if ($request->filled('budget_max')) {
            $query->where('budzets', '<=', $request->query('budget_max'));
        }

        if ($request->filled('date_from')) {
            $query->where('sakuma_datums', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('beigu_datums', '<=', $request->query('date_to'));
        }

        $sortBy = $request->query('sort_by', 'celojuma_id');
        $sortDir = $request->query('sort_dir', 'asc');
        $allowedSort = ['celojuma_id', 'nosaukums', 'galamerkis', 'sakuma_datums', 'beigu_datums', 'budzets'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'celojuma_id';
        }
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sortBy, $sortDir)->get());
    }

    public function show($id)
    {
        $celojums = Celojums::with([
            'lietotajs',
            'dienasPunkti.vieta',
            'rezervacijas',
            'izdevumi',
        ])->find($id);

        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }

        return response()->json($celojums);
    }

    public function store(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Nepieciešama autentifikācija'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nosaukums' => 'required|string|max:100',
            'galamerkis' => 'required|string|max:100',
            'valsts' => 'required|string|max:100',
            'sakuma_datums' => 'required|date|after_or_equal:today',
            'beigu_datums' => 'required|date|after_or_equal:sakuma_datums',
            'budzets' => 'required|numeric|min:0',
            
        ], [
            'nosaukums.required' => 'Nosaukums ir obligāts',
            'nosaukums.max' => 'Nosaukums pārāk garš (max 100 simboli)',
            'galamerkis.required' => 'Galamērķis ir obligāts',
            'valsts.required' => 'Valsts ir obligāts',
            'sakuma_datums.required' => 'Sākuma datums ir obligāts',
            'sakuma_datums.after_or_equal' => 'Sākuma datumam jābūt šodien vai vēlāk',
            'beigu_datums.required' => 'Beigu datums ir obligāts',
            'beigu_datums.after_or_equal' => 'Beigu datumam jābūt pēc sākuma datuma',
            'budzets.required' => 'Budžets ir obligāts',
            'budzets.min' => 'Budžets nevar būt negatīvs',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['lietotajs_id'] = $request->user()->id;

        $celojums = Celojums::create($data);

        return response()->json($celojums, 201);
    }

    public function update(Request $request, $id)
    {
        $celojums = Celojums::find($id);
        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }

        if (!$request->user() || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību rediģēt šo ceļojumu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nosaukums' => 'sometimes|required|string|max:100',
            'galamerkis' => 'sometimes|required|string|max:100',
            'valsts' => 'sometimes|required|string|max:100',
            'sakuma_datums' => 'sometimes|required|date',
            'beigu_datums' => 'sometimes|required|date|after_or_equal:sakuma_datums',
            'budzets' => 'sometimes|required|numeric|min:0',
        ], [
            'valsts.required' => 'Valsts ir obligāts',
            'beigu_datums.after_or_equal' => 'Beigu datumam jābūt pēc sākuma datuma',
            'budzets.min' => 'Budžets nevar būt negatīvs',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $celojums->update($validator->validated());

        return response()->json($celojums);
    }

    public function destroy(Request $request, $id)
    {
        $celojums = Celojums::find($id);
        if (!$celojums) {
            return response()->json(['message' => 'Ceļojums nav atrasts'], 404);
        }

        if (!$request->user() || $celojums->lietotajs_id !== $request->user()->id) {
            return response()->json(['message' => 'Nav tiesību dzēst šo ceļojumu'], 403);
        }

        $celojums->delete();

        return response()->json(['message' => 'Ceļojums dzēsts']);
    }

    public function stats()
    {
        $totalTrips = Celojums::count();
        $totalBudget = Celojums::sum('budzets');
        $avgBudget = round(Celojums::avg('budzets') ?? 0, 2);

        $byDestination = Celojums::selectRaw('galamerkis, COUNT(*) as count, SUM(budzets) as total_budget')
            ->groupBy('galamerkis')
            ->orderByDesc('count')
            ->get();

        $expensesByCategory = \App\Models\Izdevums::selectRaw('kategorija, COUNT(*) as count, SUM(summa) as total')
            ->groupBy('kategorija')
            ->orderByDesc('total')
            ->get();

        $reservationsByType = \App\Models\Rezervacija::selectRaw('tips, COUNT(*) as count, SUM(cena) as total')
            ->groupBy('tips')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = \App\Models\Izdevums::sum('summa');
        $totalReservations = \App\Models\Rezervacija::sum('cena');

        return response()->json([
            'total_trips' => $totalTrips,
            'total_budget' => $totalBudget,
            'avg_budget' => $avgBudget,
            'total_expenses' => $totalExpenses,
            'total_reservations' => $totalReservations,
            'by_destination' => $byDestination,
            'expenses_by_category' => $expensesByCategory,
            'reservations_by_type' => $reservationsByType,
        ]);
    }
}